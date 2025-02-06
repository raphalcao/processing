<?php

namespace App\Repository;

use  App\Contracts\Repositories\ProcessStatusRepositoryInterface;
use App\Models\ProcessStatus;
use Carbon\Carbon;


class ProcessStatusRepository implements ProcessStatusRepositoryInterface
{
    protected $model;

    public function __construct(ProcessStatus $processStatus)
    {
        $this->model = $processStatus;
    }

    public function insert(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Insere um novo usuário no banco de dados.
     *
     * @param int $userId
     * @param string $process
     * @param string $status
     */
    public function store(int $userId, string $status, string $process): void
    {
        $this->insert([
            'user_id' => $userId,
            'process_name' => $process,
            'status' => $status,
            'updated_at' => Carbon::now()
        ]);
    }
}
