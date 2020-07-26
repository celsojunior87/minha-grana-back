<?php


namespace App\Services;


use App\Repositories\ItemMovimentacaoRepository;


class ItemMovimentacaoService extends AbstractService
{
    protected $repository;

    public function __construct(ItemMovimentacaoRepository $repository)
    {
        $this->repository = $repository;

    }
}
