<?php

namespace App\Http\Controllers;


use App\Http\Requests\ItemRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Services\ItemService;
use Illuminate\Http\Request;


class ItemController extends AbstractController
{
    protected $requestValidate = ItemRequest::class;
    protected $requestValidateUpdate = ItemUpdateRequest::class;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function reordenar(Request $request)
    {
        return $this->success($this->service->reordenar($request->all()));
    }

    public function ajuste(Request $request)
    {
        return $this->success($this->service->ajuste($request->all()));
    }

    public function preRequisiteAjuste($date)
    {
        $preRequisiteAjuste = $this->service->preRequisiteAjuste($date);
        return $this->ok(compact('preRequisiteAjuste'));
    }
}
