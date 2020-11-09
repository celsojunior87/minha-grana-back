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
    protected $item;

    public function __construct(ItemTransferenciaRepository $repository, ItemService $itemService, Item $item)
    {
        $this->repository = $repository;
        $this->itemService = $itemService;
        $this->item = $item;

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
        $vlSaldoFinal = $this->item->getVlSaldoFinalAttribute();
        $itemDe = $this->itemService->find($transferencia['item_id_de']);
        $itemPara = $this->itemService->find($transferencia['item_id_para']);
        $vlTransferencia = $transferencia['vl_transferencia'];

        if ($vlTransferencia > $vlSaldoFinal) {
            throw new \Exception('O valor da transferencia não pode ser maior que o saldo final');
        }else{
            $transferir = [
                'item_id_de' => $itemDe,
                'item_id_para' => $itemPara,
                'vl_transferencia' => $vlTransferencia
            ];

            $this->save($transferir);

        }



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


