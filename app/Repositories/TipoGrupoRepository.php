<?php


namespace App\Repositories;


use App\Models\TipoGrupo;

class TipoGrupoRepository extends AbstractRepository
{
    protected $model;

    public function __construct(TipoGrupo $model)
    {
        $this->model = $model;
    }
}
