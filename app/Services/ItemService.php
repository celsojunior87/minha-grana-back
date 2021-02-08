<?php


namespace App\Services;


use App\Models\Item;
use App\Models\TipoGrupo;
use App\Repositories\ItemRepository;
use Carbon\Carbon;
use App\Helper\Number;

class ItemService extends AbstractService
{
    protected $repository;
    protected $itemMovimentacaoService;
    protected $itemTransferenciaService;

    public function __construct(
        ItemRepository $repository,
        ItemMovimentacaoService $itemMovimentacaoService
    )
    {
        $this->repository = $repository;
        $this->itemMovimentacaoService = $itemMovimentacaoService;
    }

    /**
     * Busca Item do mes posterior
     *
     */
    public function buscarItem(int $id)
    {
        $item = parent::find($id);
        $dataDoItem = $item->grupo()->first()->data;
        $mesSeguinte = Carbon::createFromFormat('Y-m-d', $dataDoItem)->subMonth(1)->format('Y-m');
        $search = [
            'date' => $mesSeguinte,
            'nome' => $item->nome
        ];
        $grupoService = app(GrupoService::class);
        $grupos = $grupoService->getAll($search);
        $itemUpdate = [];
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

        $itemToArray = $item->toArray();
        $itemToArray['is_disabled_saldo_inicial'] = false;
        if(!empty($itemSearch)) {
            $itemToArray['is_disabled_saldo_inicial'] = true;
        }
        return $itemToArray;
    }

    public function beforeSave(array $data)
    {
        $countItemsNoGrupo = $this->repository
            ->getModel()
            ->where('grupo_id', $data['grupo_id'])
            ->count();

        $data['ordenacao'] = $countItemsNoGrupo + 1;
        return $data;
    }

    public function delete($id)
    {

        $item = $this->repository->find($id);
        parent::delete($id);

        $this->removerTransferencia($item);
        $this->reordenarItensOnDelete($item);
        $this->reordenarItensMovimentacaoOnDelete($item);
    }

    /**
     * Ao remover um item que é transferencia
     * fazer as regras de devolucao e abatimento de valores
     * @param Item $item
     * @throws \Exception
     */
    public function removerTransferencia(Item $item)
    {

        if(!empty($item->transferencia_id)) {
            $itemTransferenciaService = app(ItemTransferenciaService::class);
            $transferencia = $itemTransferenciaService->find($item->transferencia_id);
            $itemTransferenciaService->delete($transferencia->id);
            $itemIdPara = $this->find($transferencia->item_id_para);
            $itemIdPara->vl_esperado -= $transferencia->vl_transferencia;
            $this->update($itemIdPara->id, $itemIdPara);
        }
    }

    public function reordenarItensOnDelete(Item $item)
    {
        $itemsPorGrupo = $this->repository
            ->getModel()
            ->orderBy('ordenacao')
            ->where('grupo_id', $item->grupo_id)
            ->get();

        /**
         * Ordenacao de itens
         */
        foreach ($itemsPorGrupo->toArray() as $key => $item) {
            $item['ordenacao'] = ++$key;
            parent::update($item['id'], $item);
        }
    }

    public function reordenarItensMovimentacaoOnDelete(Item $item)
    {
        $grupoService = app(GrupoService::class);
        $grupo = $grupoService->find($item->grupo_id);

        $grupos = $grupoService->getAll(
            ['date' => Carbon::createFromFormat('Y-m-d', $grupo->data)->format('Y-m')]
        );

        $movimentacoes = $grupoService->getMovimentacaoByGrupos($grupos);

        /**
         * Ordenacao de movimentacoes
         */
        foreach ($movimentacoes as $key => $movimentacao) {
            $movimentacao['ordenacao'] = ++$key;
            $this->itemMovimentacaoService->update($movimentacao['movimentacao_id'], $movimentacao);
        }
    }

    public function reordenar($items)
    {
        foreach ($items as $key => $item) {
            $objItem = $this->find($item['id']);
            $objItem->ordenacao = ++$key;
            parent::update($objItem->id, $objItem);
        }
    }

    /**
     * vl_ajuste = valor realizado - valor planejado
     * data
     * tipo de grupo
     * items
     * @param $params
     */
    public function ajuste($params)
    {
        $movimentacaDooId = '';
        $vlAjuste = 0;
        $dateAjuste = '';
        foreach ($params as $key => $param) {
            if (isset($param['form'])) {
                $vlAjuste = Number::formatCurrencyBr($param['form']['vl_ajuste']);
                $dateAjuste = $param['form']['date'];
                $movimentacaoId = $param['form']['movimentacao_id'];
                unset($params[$key]['form']);
                unset($params[$key]['valor']);
            }
        }
        $valor = 0;
        $result = 0;

        $this->verificarSeValorDosItensNaoEMaiorQueOValorAjuste(array_filter($params), $vlAjuste);
        $this->verificarSeValorAdicionadoEigualOvalorAjuste(array_filter($params), $vlAjuste);

        foreach (array_filter($params) as $param) {

            $valor = Number::formatCurrencyBr($param['valor']);
            $item = $this->find($param['id']);
            $tipoGrupoId = $item->grupo()->first()->tipoGrupo()->first()->id;

            $movimentacao = $this->itemMovimentacaoService->find($movimentacaoId);

            $itemId = $this->find($movimentacao->item_id);

            if ($tipoGrupoId == TipoGrupo::RECEITAS) {
                if ($vlAjuste > 0) {
                    $itemId->vl_esperado += $valor;
                    $item->vl_esperado += $valor;
                    parent::update($item->id, $item);
                    parent::update($itemId->id, $itemId);
                    $movimentacao->vl_planejado = $movimentacao->vl_realizado;
                    $this->itemMovimentacaoService->update($movimentacaoId, $movimentacao);
                }
                if ($vlAjuste < 0) {
                    $itemId->vl_esperado += $valor;
                    $item->vl_esperado -= $valor;
                    parent::update($item->id, $item);
                    parent::update($itemId->id, $itemId);
                    $movimentacao->vl_planejado = $movimentacao->vl_realizado;
                    $this->itemMovimentacaoService->update($movimentacaoId, $movimentacao);
                }

            } else {
                if ($vlAjuste > 0) {

                    $itemId->vl_esperado -= $valor;
                    $item->vl_esperado += $valor;
                    parent::update($item->id, $item);
                    parent::update($itemId->id, $itemId);
                    $movimentacao->vl_planejado = $movimentacao->vl_realizado;
                    $this->itemMovimentacaoService->update($movimentacaoId, $movimentacao);
                }
                if ($vlAjuste < 0) {
                    $itemId->vl_esperado += $valor;
                    $item->vl_esperado -= $valor;
                    parent::update($item->id, $item);
                    parent::update($itemId->id, $itemId);
                    $movimentacao->vl_planejado = $movimentacao->vl_realizado;
                    $this->itemMovimentacaoService->update($movimentacaoId, $movimentacao);
                }

            }


        }
    }

    public function verificarSeValorAdicionadoEigualOvalorAjuste($arrayAjuste, $vlAjuste)
    {
        $vlAjusteP = abs($vlAjuste);
        $valorTotalAjustes = 0;
        foreach ($arrayAjuste as $ajuste) {
            $valorTotalAjustes += Number::formatCurrencyBr($ajuste['valor']);
        }

        if (Number::formatCurrencyBr($valorTotalAjustes) != $vlAjusteP) {
            throw new \Exception('O valor Inserido é Menor que o valor a ser ajustado');
        }
    }

    public function verificarSeValorDosItensNaoEMaiorQueOValorAjuste($arrayAjuste, $vlAjuste)
    {
        $vlAjusteP = abs($vlAjuste);
        $valorTotalAjustes = 0;
        foreach ($arrayAjuste as $ajuste) {
            $valorTotalAjustes += Number::formatCurrencyBr($ajuste['valor']);
        }

        if (Number::formatCurrencyBr($valorTotalAjustes) > $vlAjusteP) {
            throw new \Exception('O valor do ajuste é diferente do valor total');
        }

    }

    /**
     * Busca grupos e seus items
     * grupos : [
     *      {
     *          id: 1,
     *          nome: 'Receita',
     *          items: [
     *              {
     *                  id: 1,
     *                  nome: Datainfo
     *              }
     *          ]
     *      }
     *  ]
     * @param $date
     * @return mixed
     */
    public function preRequisiteAjuste($date)
    {
        $grupoService = app(GrupoService::class);
        $grupos = $grupoService->getAll(['date' => $date]);
        $arr['grupos'] = [];

        if (!empty($grupos)) {
            foreach ($grupos as $key => $grupo) {
                $arr['grupos'][$key]['id'] = $grupo['id'];
                $arr['grupos'][$key]['nome'] = $grupo['nome'];
                foreach ($grupo['items'] as $keyItems => $item) {
                    $arr['grupos'][$key]['items'][$keyItems]['id'] = $item['id'];
                    $arr['grupos'][$key]['items'][$keyItems]['color'] = ($grupo['tipo_grupo']['id'] == TipoGrupo::RECEITAS) ? '#6FCF97' : '#F57077';
                    $arr['grupos'][$key]['items'][$keyItems]['nome'] = $item['nome'];
                }
            }
        }
        return $arr;
    }


}
