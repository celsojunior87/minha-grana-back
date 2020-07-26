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

        if(!$itemMovimentacao) {
            $this->itemMovimentacaoService->save(['item_id' => $id]);
        }
        return parent::update($id, $data);
    }
}
