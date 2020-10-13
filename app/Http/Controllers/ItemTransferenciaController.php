<?php

namespace App\Http\Controllers;


use App\Services\ItemTransferenciaService;
use Illuminate\Http\Request;

class ItemTransferenciaController extends Controller
{

    public function __construct(ItemTransferenciaService $service)
    {
        $this->service = $service;
    }
}
