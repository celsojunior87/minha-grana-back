<?php


namespace App\Services;


use App\Repositories\GrupoRepository;

class GrupoService
{
    protected $repository;

    public function __construct(
        GrupoRepository $repository
          )
    {
        $this->repository = $repository;

    }

//    /**
//     * @return mixed
//     */
//    public function preRequisite()
//    {
//        $arr['tipoProduto'] = generateSelectOption($this->tipoProdutoService->getRepository()->list());
//        $arr['categoria'] = generateSelectOption($this->categoriaService->getRepository()->list());
//        return $arr;
//    }
}