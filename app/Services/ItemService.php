<?php


namespace App\Services;


use App\Models\Item;
use App\Models\TipoGrupo;
use App\Repositories\ItemRepository;
use Carbon\Carbon;

class ItemService extends AbstractService
{
    protected $repository;
    protected $itemMovimentacaoService;

    public function __construct(ItemRepository $repository, ItemMovimentacaoService $itemMovimentacaoService)
    {
        $this->repository = $repository;
        $this->itemMovimentacaoService = $itemMovimentacaoService;
    }

    public function update($id, $data)
    {
        $itemMovimentacao = $this->itemMovimentacaoService
            ->getRepository()
            ->getModel()
            ->firstWhere(['item_id' => $id]);

        if (!$itemMovimentacao) {
            $this->itemMovimentacaoService->save(['item_id' => $id]);
        }
        return parent::update($id, $data);
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

        $this->reordenarItensOnDelete($item);
        $this->reordenarItensMovimentacaoOnDelete($item);
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

    public function ajuste($params)
    {

        dd('cegou');
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

        if(!empty($grupos)) {
            foreach ($grupos as $key => $grupo) {
                if(!empty($grupo['items'])) {
                    $arr['grupos'][$key]['id'] = $grupo['id'];
                    $arr['grupos'][$key]['nome'] = $grupo['nome'];
                    foreach ($grupo['items'] as $keyItems => $item) {
                        $arr['grupos'][$key]['items'][$keyItems]['id'] = $item['id'];
                        $arr['grupos'][$key]['items'][$keyItems]['color'] =
                            ($grupo['tipo_grupo']['id'] == TipoGrupo::RECEITAS) ? 'green' : 'red';
                        $arr['grupos'][$key]['items'][$keyItems]['nome'] = $item['nome'];
                    }
                }
            }
        }
        return $arr;
    }
}
