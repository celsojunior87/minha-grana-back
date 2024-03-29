<?php


namespace App\Services;


use App\Helper\Number;
use App\Models\Item;
use App\Models\ItemTransferencia;
use App\Models\TipoGrupo;
use App\Models\TipoItem;
use App\Repositories\ItemMovimentacaoRepository;
use App\Repositories\ItemTransferenciaRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ItemTransferenciaService extends AbstractService
{
    protected $repository;
    protected $itemService;
    protected $grupoService;

    public function __construct(ItemTransferenciaRepository $repository, ItemService $itemService, GrupoService $grupoService)
    {
        $this->repository = $repository;
        $this->itemService = $itemService;
        $this->grupoService = $grupoService;
    }

    public function economia($params)
    {
        $item = [
            'id' => $params['item_id'],
            'vl_saldo_inicial' => $params['vl_saldo_inicial'],
            'vl_esperado' => $params['vl_esperado'],
            'vl_gasto' => $params['vl_gasto'],
            'vl_total_objetivo' => $params['vl_total_objetivo'],
            'tipo_item_id' => TipoItem::ECONOMIA,
        ];
        $item = $this->itemService->update($item['id'], $item);
        $this->atualizarMesSeguinte($item);
    }

    public function divida($params)
    {
        $item = [
            'id' => $params['item_id'],
            'vl_saldo_inicial' => $params['vl_saldo_inicial'],
            'vl_esperado' => $params['vl_esperado'],
            'pagamento_minimo' => $params['pagamento_minimo'],
            'juros_multas' => $params['juros_multas'],
            'tipo_item_id' => TipoItem::DIVIDA,
        ];

        $item = $this->itemService->update($item['id'], $item);
        $this->atualizarMesSeguinte($item);
    }

    public function atualizarMesSeguinte(Item $item)
    {
        $dataDoItem = $item->grupo()->first()->data;
        $mesSeguinte = Carbon::createFromFormat('Y-m-d', $dataDoItem)->addMonth(1)->format('Y-m');
        $search = [
            'date' => $mesSeguinte,
            'nome' => $item->nome
        ];
        $itemUpdate = [];
        $grupos = $this->grupoService->getAll($search);
        foreach($grupos as $grupo) {
            $itemUpdate[] = array_filter($grupo['items'], function($itemArray) use ($item) {
                return $itemArray['nome'] == $item->nome;
            });
        }

        $itemSearch = [];
        foreach($itemUpdate as $update) {
            if(!empty($update)) {
                sort($update);
                $itemSearch = $update[0];
            }
        }

        if(!empty($itemSearch)) {
            $itemModel = $this->itemService->buscarItem($itemSearch['id']);
            $itemModel['vl_saldo_inicial'] = $item['vl_saldo_final'];
            $this->itemService->update($itemModel['id'], $itemModel);
        }
    }

    public function transferir(array $transferencia)
    {
        $itemDe = $this->itemService->find($transferencia['item_id_de']);
        $itemPara = $this->itemService->find($transferencia['item_id_para']['id']);
        $vlTransferencia = $transferencia['vl_transferencia'];
        $vlSaldoFinal = $itemDe->getVlSaldoFinalAttribute();

        if ($vlTransferencia > $vlSaldoFinal) {
            throw new \Exception('O valor da transferência não pode ser maior que o saldo final');
        }

        $transferir = [
            'item_id_de' => $itemDe->id,
            'item_id_para' => $itemPara->id,
            'vl_transferencia' => $vlTransferencia
        ];

        $itemTransferencia = $this->save($transferir);
        $this->criarItemTransferido($itemTransferencia);
        $this->atualizarParaItemDestinadoTransferido($itemTransferencia);
    }

    public function atualizarParaItemDestinadoTransferido(ItemTransferencia $itemTransferencia)
    {
        $itempara = $this->itemService->find($itemTransferencia->item_id_para);
        $itempara->vl_esperado += $itemTransferencia->vl_transferencia;

        $this->itemService->update($itempara->id, $itempara);
    }


    public function criarItemTransferido(ItemTransferencia $itemTransferencia)
    {
        $itemDe = $this->itemService->find($itemTransferencia->item_id_de);
        $grupo = $this->grupoService->find($itemDe->grupo_id);
        $dataDoItem = $grupo->data;

        $search = [
            'date' => Carbon::createFromFormat('Y-m-d', $dataDoItem)->format('Y-m'),
            'tipo_grupo' => TipoGrupo::RECEITAS
        ];

        $idGrupoReceita = $this->grupoService->getRepository()->movimentacao($search)[0]['id'];

        $arrItemTransferencia = [
            'transferencia_id' => $itemTransferencia->id,
            'vl_esperado' => $itemTransferencia->vl_transferencia,
            'grupo_id' => $idGrupoReceita,
            'nome' => 'Transferência de ' . $itemDe->nome,
            'item_id_de' => $itemTransferencia->item_id_de,
            'item_id_para' => $itemTransferencia->item_id_para,
        ];
        $this->criarOuAtualizarTransferencia($arrItemTransferencia);
    }

    /**
     * Se já existir uma transferencia, entao apenas atualiza o item
     * @param array $itemTransferencia
     * @throws \Exception
     */
    public function criarOuAtualizarTransferencia(array $itemTransferencia)
    {
        $primeiraTransferenciaDoItem = $this->repository->buscaPrimeiraTransferenciaDoItem($itemTransferencia);
        $primeiroItemTransferido = $this->itemService->getRepository()->buscaItemJaTransferido($primeiraTransferenciaDoItem->id);
        if ($primeiroItemTransferido) {
            $primeiroItemTransferido->vl_esperado += $itemTransferencia['vl_esperado'];
            $this->itemService->update($primeiroItemTransferido->id, $primeiroItemTransferido);
            return;
        }
        $this->itemService->save($itemTransferencia);
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


