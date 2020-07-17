<?php


namespace App\Repositories;

use App\Helper\Number;
use App\Models\Item;

class ItemRepository extends AbstractRepository
{
    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
    }

    public function formatParams($params)
    {
        $formatted = [];

        if (isset($params['vl_esperado'])) {
            $formatted['vl_esperado'] = Number::formatCurrencyBr($params['vl_esperado']);
        }

        return $formatted;
    }
}
