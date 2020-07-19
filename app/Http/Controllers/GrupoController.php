<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrupoRequest;
use App\Services\GrupoService;
use Illuminate\Http\Request;

class GrupoController extends AbstractController
{
    protected $requestValidate = GrupoRequest::class;

    public function __construct(GrupoService $service)
    {
        $this->service = $service;
    }

    public function getMovimentacao(Request $request)
    {
        $this->service->movimentacao($request->all());
    }

}
