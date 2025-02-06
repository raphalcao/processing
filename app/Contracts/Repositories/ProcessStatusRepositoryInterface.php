<?php

namespace App\Contracts\Repositories;

use Aws\Result;

interface ProcessStatusRepositoryInterface
{
    /**
     * Insere um processo de status no banco de dados.
     *
     * @param int $userId O id do usuário.
     * @param string $process a descricao processo.
     * @param string $status O status do processo.
     */
    public function store(int $userId, string $process, string $status): void;
}
