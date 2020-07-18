<?php


namespace App\Services;


use App\Repositories\TipoGrupoRepository;

class TipoGrupoService extends AbstractService
{
    protected $repository;

    public function __construct(TipoGrupoRepository $repository)
    {
        $this->repository = $repository;
    }

}
