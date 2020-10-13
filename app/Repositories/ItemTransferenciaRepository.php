<?php


namespace App\Repositories;

use App\Helper\Number;
use App\Models\Item;
use App\Models\ItemMovimentacao;
use App\Models\ItemTransferencia;

class ItemTransferenciaRepository extends AbstractRepository
{
    protected $model;

    public function __construct(ItemTransferencia $model)
    {
        $this->model = $model;
    }

    public function formatParams($params)
    {
        $formatted = [];

        return $formatted;
    }
}
