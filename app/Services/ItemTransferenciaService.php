<?php


namespace App\Services;


use App\Models\Item;
use App\Repositories\ItemMovimentacaoRepository;
use App\Repositories\ItemTransferenciaRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ItemTransferenciaService extends AbstractService
{
    protected $repository;
    protected $item;

    public function __construct(ItemTransferenciaRepository $repository, ItemService $item)
    {
        $this->repository = $repository;
        $this->item = $item;
    }

    public function economia($params)
    {
        $item = [
            'id' => $params['item_id'],
            'vl_saldo_inicial' => $params['vl_saldo_inicial'],
            'vl_gasto' => $params['vl_gasto'],
            'vl_total_objetivo' => $params['vl_total_objetivo']
        ];

        dd($item);
        $this->item->update($item['id'], $item);

    }


}
