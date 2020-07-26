<?php


namespace App\Repositories;

use App\Helper\Number;
use App\Models\Item;
use App\Models\ItemMovimentacao;

class ItemMovimentacaoRepository extends AbstractRepository
{
    protected $model;

    public function __construct(ItemMovimentacao $model)
    {
        $this->model = $model;
    }

    public function formatParams($params)
    {
        $formatted = [];

        $formatted['descricao'] = '$params[]';

        if (isset($params['item_id'])) {
            $formatted['item_id'] = $params['item_id'];
        }

        if (isset($params['vl_planejado'])) {
            $formatted['vl_planejado'] = Number::formatCurrencyBr($params['vl_planejado']);
        }
        if (isset($params['vl_saldo_esperado'])) {
            $formatted['vl_saldo_esperado'] = Number::formatCurrencyBr($params['vl_saldo_esperado']);
        }

        if (isset($params['vl_realizado'])) {
            $formatted['vl_realizado'] = Number::formatCurrencyBr($params['vl_realizado']);
        }

        if (isset($params['data'])) {
            $formatted['data'] = $params['data'];
        }

        return $formatted;
    }
}
