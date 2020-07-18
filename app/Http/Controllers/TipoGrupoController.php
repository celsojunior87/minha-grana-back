<?php

namespace App\Http\Controllers;

use App\Services\GrupoService;
use App\Services\TipoGrupoService;
use Illuminate\Http\Request;

class TipoGrupoController extends AbstractController
{
    public function __construct(TipoGrupoService $service)
    {
        $this->service = $service;
    }
}
