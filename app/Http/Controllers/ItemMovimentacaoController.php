<?php

namespace App\Http\Controllers;

use App\Services\ItemMovimentacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ItemMovimentacaoController extends AbstractController
{
    public function __construct(ItemMovimentacaoService $service)
    {
        $this->service = $service;
    }

    /**
     * Criar um item movimentacao
     * @param $idItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function criarItemMovimentacao($idItem)
    {
        try {
            DB::beginTransaction();
            $response = $this->service->criarItemMovimentacao($idItem);
            DB::commit();
            return $this->success('Operação realizada com com sucesso', ['response' => $response]);
        } catch (\Exception | ValidationException $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                return $this->error('Ops', $e->errors());
            }
            if ($e instanceof \Exception) {
                return $this->error($e->getMessage());
            }
        }
    }

    public function reordenar(Request $request)
    {
        return $this->success($this->service->reordenar($request->all()));
    }
}
