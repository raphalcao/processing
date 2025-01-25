<?php

namespace App\Repository;

abstract class AbstractRepository
{
    /**
     * O modelo associado ao repositório.
     */
    protected $model;

    /**
     * Encontra um registro pelo campo e valor fornecidos.
     *
     * @param string $field O campo a ser pesquisado (ex: 'email', 'token').
     * @param mixed $value O valor do campo (ex: 'example@example.com').
     * @return mixed
     */
    public function find(string $field, $value)
    {
        return $this->model::where($field, $value)->first();
    }
}
