<?php


namespace App\Repositories;


use App\Models\Grupo;

class GrupoRepository extends AbstractRepository
{
    protected $model;

    public function __construct(Grupo $model)
    {
        $this->model = $model;
    }
}
