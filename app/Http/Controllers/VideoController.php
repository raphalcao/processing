<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VideoProcessingService;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoProcessingService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function processVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi|max:51200',
        ]);

        $videoFile = $request->file('video');

        try {
            $zipFile = $this->videoService->processVideo($videoFile);
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
