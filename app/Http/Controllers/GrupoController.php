<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrupoRequest;
use App\Http\Requests\GrupoUpdateRequest;
use App\Services\GrupoService;
use Illuminate\Http\Request;

class GrupoController extends AbstractController
{
    protected $requestValidate = GrupoRequest::class;
    protected $requestValidateUpdate = GrupoUpdateRequest::class;

    public function __construct(GrupoService $service)
    {
        $this->service = $service;
    }

    public function getMovimentacao(Request $request)
    {
        return $this->service->movimentacao($request->all());
    }

    public function criarMes(Request $request)
    {
        $this->service->criarMes($request->all());
        return $this->success('Operação realizada com com sucesso');
    }
}
