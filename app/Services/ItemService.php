<?php


namespace App\Services;


use App\Repositories\ItemRepository;

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
        $grupo_id = $this->repository->find($id)->grupo_id;
        parent::delete($id);

        $itemsPorGrupo = $this->repository
            ->getModel()
            ->orderBy('ordenacao')
            ->where('grupo_id', $grupo_id)
            ->get();

        foreach ($itemsPorGrupo->toArray() as $key => $item) {
            $item['ordenacao'] = ++$key;
            parent::update($item['id'], $item);
        }
    }

    public function reordenar($items)
    {
        foreach ($items as $key => $item) {
            $objItem = $this->find($item['item_id']);
            $objItem->ordenacao = ++$key;
            $this->update($objItem->id, $objItem);
        }
    }
}
