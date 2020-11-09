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

        if (isset($params['item_id_de'])) {
            $formatted['item_id_de'] = $params['item_id_de'];
        }
        if (isset($params['item_id_para'])) {
            $formatted['item_id_para'] = $params['item_id_para'];
        }
        if (isset($params['vl_transferencia'])) {
            $formatted['vl_transferencia'] = $params['vl_transferencia'];
        }

        return $formatted;
    }
}
