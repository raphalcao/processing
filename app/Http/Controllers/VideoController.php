<?php

namespace App\Http\Controllers;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class VideoController extends Controller
{
    public function processVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi|max:51200', // Limite de 50MB
        ]);

        $videoFile = $request->file('video');

        // Salvar o vídeo temporariamente
        $videoPath = $videoFile->store('videos', 'local');
        $videoFullPath = storage_path("app/$videoPath");

        $framesDirectory = storage_path('app/frames');
        if (!file_exists($framesDirectory)) {
            mkdir($framesDirectory, 0777, true);
        }

        try {
            // Processar o vídeo e gerar frames
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($videoFullPath);

            $video->frame(TimeCode::fromSeconds(1))
                ->save("$framesDirectory/frame1.jpg");
            $video->frame(TimeCode::fromSeconds(2))
                ->save("$framesDirectory/frame2.jpg");

            // Criar um arquivo ZIP com os frames
            $zip = new ZipArchive();
            $zipFileName = storage_path('app/frames.zip');
            if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
                foreach (glob("$framesDirectory/*.jpg") as $frame) {
                    $zip->addFile($frame, basename($frame));
                }
                $zip->close();
            }

            // Limpar arquivos temporários
            Storage::deleteDirectory('frames');
            Storage::delete($videoPath);

            // Retornar o arquivo ZIP como resposta
            return response()->download($zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Storage::deleteDirectory('frames');
            Storage::delete($videoPath);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
