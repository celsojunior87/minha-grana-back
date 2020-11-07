<?php


namespace App\Services;


use App\Helper\Number;
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

    public function transferir(array $transferencia)
    {
        $itemDe = $this->itemService->find($transferencia['item_id_de']);
        $itemPara = $this->itemService->find($transferencia['item_id_para']);
        $vlTransferencia = $transferencia['vl_transferencia'];

//       todo fazer a regra de negócio
    }

    public function preRequisite(int $id)
    {
        $item = $this->itemService->find($id, ['grupo'])->toArray();
        $selectOption = $this
            ->itemService
            ->getRepository()
            ->preRequisiteItemTransferenciaNotInSelfAndOnlyDespesas($item);

        return generateSelectOption($selectOption);
    }
}


