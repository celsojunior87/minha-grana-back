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
    protected $itemService;

    public function __construct(ItemTransferenciaRepository $repository, ItemService $itemService)
    {
        $this->repository = $repository;
        $this->itemService = $itemService;
    }

    public function economia($params)
    {
        $item = [
            'id' => $params['item_id'],
            'vl_saldo_inicial' => $params['vl_saldo_inicial'],
            'vl_esperado' => $params['vl_esperado'],
            'vl_gasto' => $params['vl_gasto'],
            'vl_total_objetivo' => $params['vl_total_objetivo'],
        ];
        $this->itemService->update($item['id'], $item);

    }
    /**
     * @return mixed
     */
    public function preRequisiteTransferencia()
    {
        $arr['item'] = generateSelectOption($this->itemService->getRepository()->list('id'));

        dd($arr);
        return $arr;
    }


}
