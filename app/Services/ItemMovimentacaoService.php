<?php


namespace App\Services;


use App\Repositories\ItemMovimentacaoRepository;


class ItemMovimentacaoService extends AbstractService
{
    protected $repository;

    public function __construct(ItemMovimentacaoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function criarItemMovimentacao($idItem)
    {
        $data['item_id'] = $idItem;
        $this->save($data);
    }

    public function beforeSave(array $data)
    {
        $countItemsMovimentacaoNoGrupo = $this->repository
            ->getModel()
            ->where('item_id', $data['item_id'])
            ->count();
        $data['ordenacao'] = $countItemsMovimentacaoNoGrupo + 1;
        return $data;
    }

    public function delete($id)
    {
        $item_id = $this->repository->find($id)->grupo_id;
        parent::delete($id);
        $itemsPorGrupo = $this->repository
            ->getModel()
            ->orderBy('ordenacao')
            ->where('item_id', $item_id)
            ->get();

        foreach ($itemsPorGrupo->toArray() as $key => $item) {
            $item['ordenacao'] = ++$key;
            parent::update($item['id'], $item);
        }

    }
}
