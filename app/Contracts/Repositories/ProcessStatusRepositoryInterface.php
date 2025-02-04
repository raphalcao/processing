<?php

namespace App\Contracts\Repositories;

use Aws\Result;

interface ProcessStatusRepositoryInterface
{
    /**
     * Insere um processo de status no banco de dados.
     *
     * @param string $cognitoUserId O ID do usuário no Cognito.
     * @param string $email O email do usuário.
     * @param string $name O nome do usuário.
     * @param string $password O password do usuário.
     * @return string Mensagem de sucesso ou erro.
     * @return string Array response.
     */
    public function insert(string $cognitoUserId, string $email, string $password, string $name, Result $response): string;
}
