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
        if (isset($params['status_id'])) {
            $formatted['status_id'] = $params['status_id'];
        }
        if (isset($params['ordenacao'])) {
            $formatted['ordenacao'] = $params['ordenacao'];
        }

        if (isset($params['vl_saldo_inicial'])) {
            $formatted['vl_saldo_inicial'] = $params['vl_saldo_inicial'];
        }

        if (isset($params['vl_total_objetivo'])) {
            $formatted['vl_total_objetivo'] = $params['vl_total_objetivo'];
        }

        if (isset($params['vl_gasto'])) {
            $formatted['vl_gasto'] = $params['vl_gasto'];
        }
        if (isset($params['transferencia_id'])) {
            $formatted['transferencia_id'] = $params['transferencia_id'];
        }
        return $formatted;
    }

    public function preRequisiteItemTransferenciaNotIn(int $id)
    {
        return $this->model->whereNotIn('id', [$id])->pluck('nome', 'id')->all();
    }
}
