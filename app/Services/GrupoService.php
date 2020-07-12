<?php


namespace App\Services;


use App\Repositories\GrupoRepository;

class GrupoService extends AbstractService
{
    protected $repository;

    public function __construct(GrupoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll($params = null, $with = null)
    {
        return parent::getAll($params, ['items']);
    }
}
