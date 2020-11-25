<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrupoRequest;
use App\Http\Requests\GrupoUpdateRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends AbstractController
{
    protected $requestValidate = GrupoRequest::class;
    protected $requestValidateUpdate = GrupoUpdateRequest::class;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function grupos(Request $request)
    {
        return $this->ok($this->service->montarDashboardByGrupos($request->all()));

    }
}
