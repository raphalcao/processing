<?php

namespace App\Services\Support;

use MyCLabs\Enum\Enum;

class VideoProcessEnum extends Enum
{
    public const START = 'start';

    public const FINISHED = 'finished';

    public const ERROR = 'error';

    public const START_PROCESS = 'starting process';

    public const PROCESSING = 'processing';

    public const PROCESS_FINISHED = 'process finished';

    public const FAILED_PROCESS = 'error in process execution';
}
