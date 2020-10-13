<?php


namespace App\Services;


use App\Models\Item;
use App\Repositories\ItemMovimentacaoRepository;
use App\Repositories\ItemTransferenciaRepository;
use Carbon\Carbon;


class ItemTransferenciaService extends AbstractService
{
    protected $repository;

    public function __construct(ItemTransferenciaRepository $repository)
    {
        $this->repository = $repository;
    }


}
