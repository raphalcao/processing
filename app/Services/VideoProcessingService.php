<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class VideoProcessingService
{
    public function processVideo($videoFile)
    {
        if (!Storage::disk('local')->exists('videos')) {
            Storage::disk('local')->makeDirectory('videos');
        }

        $videoPath = $videoFile->store('videos', 'local');
        $videoFullPath = str_replace('/', DIRECTORY_SEPARATOR, storage_path("app/private/$videoPath"));

        if (!file_exists($videoFullPath)) {
            throw new \Exception("Arquivo de vídeo não encontrado em $videoFullPath");
        }

        $framesDirectory = storage_path('app/private/frames');
        if (!file_exists($framesDirectory)) {
            mkdir($framesDirectory, 0777, true);
        }

        try {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($videoFullPath);

            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save("$framesDirectory/frame1.jpg");
            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(2))
                ->save("$framesDirectory/frame2.jpg");

            $zip = new \ZipArchive();
            $zipFileName = storage_path('app/private/frames.zip');

            if ($zip->open($zipFileName, \ZipArchive::CREATE) === true) {
                foreach (glob("$framesDirectory/*.jpg") as $frame) {
                    $zip->addFile($frame, basename($frame));
                }
                $zip->close();
            } else {
                throw new \Exception("Falha ao criar o arquivo ZIP.");
            }

            Storage::deleteDirectory('private/frames');
            Storage::delete($videoPath);

            return $zipFileName;
        } catch (\Exception $e) {
            Storage::deleteDirectory('private/frames');
            Storage::delete($videoPath);
            throw $e;
        }
    }
}
