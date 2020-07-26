<?php

namespace App\Http\Controllers;

use App\Services\ItemMovimentacaoService;


class ItemMovimentacaoController extends AbstractController
{
    public function __construct(ItemMovimentacaoService $service)
    {
        $this->service = $service;
    }
}
