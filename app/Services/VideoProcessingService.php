<?php

namespace App\Services;


use App\Services\{
    LogService,
    ProcessStatusService
};

use Illuminate\Support\Facades\Storage;

class VideoProcessingService
{
    private const PRIVATE_DIRECTORY = "app/private";
    public const START = 'start';
    public const FINISHED = 'finished';
    public const ERROR = 'error';
    public const START_PROCESS = 'starting process';
    public const PROCESS_FINISHED = 'process finished';
    public const FAILED_PROCESS = 'error in process execution';

    private LogService $logService;
    private ProcessStatusService $processStatusService;

    public function __construct(
        LogService $logService,
        ProcessStatusService $processStatusService
    ) {
        $this->logService = $logService;
        $this->processStatusService = $processStatusService;
    }

    public function processVideo($videoFile)
    {
        $this->processStatusService->saveProcessStatus(self::START, self::START_PROCESS);
        if (!Storage::disk('local')->exists('videos')) {
            Storage::disk('local')->makeDirectory('videos');
        }

        $videoPath = $videoFile->store('videos', 'local');
        $videoFullPath = str_replace('/', DIRECTORY_SEPARATOR, storage_path(self::PRIVATE_DIRECTORY . "/$videoPath"));

        if (!file_exists($videoFullPath)) {
            $this->processStatusService->saveProcessStatus(self::ERROR, "Video file not found in $videoFullPath");
            throw new \Exception("Video file not found in $videoFullPath");
        }

        $framesDirectory = storage_path(self::PRIVATE_DIRECTORY . "/frames");
        if (!file_exists($framesDirectory)) {
            mkdir($framesDirectory, 0777, true);
        }

        try {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($videoFullPath);

            $ffprobe = \FFMpeg\FFProbe::create();
            $duration = $ffprobe->format($videoFullPath)->get('duration');

            if (!$duration) {
                $this->processStatusService->saveProcessStatus(self::ERROR, "Could not retrieve video duration.");
                throw new \Exception("Could not retrieve video duration.");
            }

            for ($second = 0; $second <= $duration; $second += 5) {
                $frameFileName = "$framesDirectory/frame_$second.jpg";
                $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($second))->save($frameFileName);
            }

            if ($duration % 5 !== 0) {
                $lastFrameFileName = "$framesDirectory/frame_last.jpg";
                $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds((int)$duration))->save($lastFrameFileName);
            }

            $dateTime = date('YmdHis');
            $zip = new \ZipArchive();
            $zipFileName = storage_path(self::PRIVATE_DIRECTORY . "/frames_$dateTime.zip");

            if ($zip->open($zipFileName, \ZipArchive::CREATE) === true) {
                foreach (glob("$framesDirectory/*.jpg") as $frame) {
                    $zip->addFile($frame, basename($frame));
                }
                $zip->close();
            } else {
                $this->processStatusService->saveProcessStatus(self::ERROR, "Failed to create file ZIP.");
                throw new \Exception("Failed to create file ZIP.");
            }

            Storage::deleteDirectory('private/frames');
            Storage::delete($videoPath);
            $this->processStatusService->saveProcessStatus(self::FINISHED, self::PROCESS_FINISHED);
            return $zipFileName;
        } catch (\Exception $e) {
            $this->processStatusService->saveProcessStatus(self::ERROR, "Error.");
            Storage::deleteDirectory('private/frames');
            Storage::delete($videoPath);
            throw $e;
        }
    }
}
