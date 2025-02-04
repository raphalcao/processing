<?php

namespace App\Services;

use App\Contracts\Repositories\{ProcessStatusRepositoryInterface, UserRepositoryInterface};

class ProcessStatusService
{

    private UserRepositoryInterface $userRepositoryInterface;
    private ProcessStatusRepositoryInterface $processStatusRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, ProcessStatusRepositoryInterface $processStatusRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->processStatusRepositoryInterface = $processStatusRepositoryInterface;
    }

    public function saveProcessStatus($process, $status)
    {

        $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
        $user = $this->userRepositoryInterface->findBytoken($token);
    }
}
