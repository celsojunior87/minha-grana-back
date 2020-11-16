<?php

namespace App\Http\Controllers;


use App\Services\ItemService;
use App\Services\ItemTransferenciaService;
use Illuminate\Http\Request;

class ItemTransferenciaController extends AbstractController
{

    public function __construct(ItemTransferenciaService $service, ItemService $itemService)
    {
        $this->service = $service;
        $this->itemService = $itemService;
    }

    public function itemTransferencia($id)
    {
        return $this->itemService->find($id);
    }

    public function economiaTransferencia(Request $request)
    {
        return $this->success($this->service->economia($request->all()));
    }

    public function transferir(Request $request)
    {
        return $this->success($this->service->transferir($request->all()));
    }

    public function divida(Request $request)
    {
        return $this->success($this->service->divida($request->all()));
    }
}
