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
     * Encontra um registro pelo campo e valor fornecidos.
     *
     * @param string $field O campo a ser pesquisado.
     * @param mixed $value O valor do campo.
     * @return mixed
     */
    public function find(string $field, $value);

    /**
     * Atualiza os dados de acesso do usuário no banco de dados.
     *
     * @param string $email O email do usuário.
     * @param Result $response A resposta do AWS Cognito.
     * @return void
     */
    public function update(string $email, Result $response): void;

    /**
     * Localiza o usuário no banco de dados.
     *
     * @param token $token O token do usuário.
     */
    public function findBytoken(string $token);
}
