<?php


namespace App\Services;


use App\Models\Item;
use App\Repositories\ItemMovimentacaoRepository;
use Carbon\Carbon;


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
        $item = Item::find($data['item_id']);
        $params['date'] = Carbon::createFromFormat('Y-m-d', $item->grupo()->first()->data)->format('Y-m');
        $grupoService = app(GrupoService::class);
        $grupos = $grupoService->getAll($params);
        $itemsMovimentacoes = $grupoService->getMovimentacaoByGrupos($grupos);
        $data['ordenacao'] = count($itemsMovimentacoes) + 1;
        return $data;
    }

}
