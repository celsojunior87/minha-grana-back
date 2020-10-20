<?php

namespace App\Http\Controllers;


use App\Services\ItemService;
use App\Services\ItemTransferenciaService;
use Illuminate\Http\Request;

class ItemTransferenciaController extends Controller
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
}
