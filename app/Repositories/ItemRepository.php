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

        if (isset($params['nome'])) {
            $formatted['nome'] = $params['nome'];
        }

        if (isset($params['grupo_id'])) {
            $formatted['grupo_id'] = $params['grupo_id'];
        }

        if (isset($params['vl_esperado'])) {
            $formatted['vl_esperado'] = Number::formatCurrencyBr($params['vl_esperado']);
        }
        if (isset($params['vl_planejado'])) {
            $formatted['vl_planejado'] = Number::formatCurrencyBr($params['vl_planejado']);
        }
        if (isset($params['data'])) {
            $formatted['data'] = $params['data'];
        }
        if (isset($params['vl_saldo_realizado'])) {
            $formatted['vl_saldo_realizado'] = Number::formatCurrencyBr($params['vl_saldo_realizado']);
        }

        return $formatted;
    }
}
