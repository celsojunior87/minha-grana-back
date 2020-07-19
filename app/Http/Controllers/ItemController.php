<?php

namespace App\Http\Controllers;


use App\Services\ItemService;


class ItemController extends AbstractController
{
    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

}
