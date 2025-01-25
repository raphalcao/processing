<?php

namespace App\Contracts\Repositories;

use Aws\Result;

interface UserRepositoryInterface
{
    /**
     * Insere um novo usuário no banco de dados.
     *
     * @param string $cognitoUserId O ID do usuário no Cognito.
     * @param string $email O email do usuário.
     * @param string $name O nome do usuário.
     * @param string $password O password do usuário.
     * @return string Mensagem de sucesso ou erro.
     * @return string Array response.
     */
    public function insert(string $cognitoUserId, string $email, string $password, string $name, Result $response): string;

    /**
     * Localiza o usuário no banco de dados.
     *
     * @param string $email O email do usuário.
     */
    public function find(string $email);

    /**
     * Atualiza os dados de acesso do usuário no banco de dados.
     *
     * @param string $email O email do usuário.
     * @param Result $response A resposta do AWS Cognito.
     * @return void
     */
    public function update(string $email, Result $response): void;
}
