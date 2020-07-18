<?php

namespace App\Http\Controllers;


use App\Http\Requests\ItemRequest;
use App\Services\ItemService;


class ItemController extends AbstractController
{
    protected $requestValidate = ItemRequest::class;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }
}
