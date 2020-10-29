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
    protected $itemTransferenciaService;

    public function __construct(ItemTransferenciaRepository $repository, ItemService $itemService, ItemTransferenciaService $itemTransferenciaService)
    {
        $this->repository = $repository;
        $this->itemService = $itemService;
        $this->itemTransferenciaService = $itemTransferenciaService;

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
        return $arr;
    }

    public function transferir($params)
    {
        $objetivo = $this->itemService->find($params['vl_total_objetivo']);
        $transferencia = [
            'id' => $params['id'],
            'item_id_de' => $params['item_id_de'],
            'item_id_para' => $params['item_id_para'],
            'vl_transferencia' => $params['vl_transferencia']
        ];

        if ($transferencia['vl_transferencia'] < $objetivo) {
            throw new \Exception('O valor da transferencia não pode ser maior que o valor do Objetivo');
        } else {
            $this->itemTransferenciaService->save($transferencia);
        }
    }


}


