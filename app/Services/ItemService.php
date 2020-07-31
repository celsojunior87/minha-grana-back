<?php


namespace App\Services;


use App\Models\Item;
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
            $this->update($objItem->id, $objItem);
        }
    }
}
