<?php


namespace App\Repositories;


use App\Models\Grupo;

class GrupoRepository
{
    protected $model;
    public function __construct(Grupo $model)
    {
        $this->model = $model;
    }
}