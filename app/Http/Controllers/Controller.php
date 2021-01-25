<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $messageSuccessDefault = 'Operação realizada com sucesso';
    protected $messageErrorDefault = 'Ops';

    /**
     * Used to return success response
     * @return Response
     */
    public function ok($items = null)
    {
        return response()->json($items);
    }

    /**
     * @param null $items
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message = null, $items = null, $status = 422)
    {
        if(is_null($message)) {
            $message = $this->messageErrorDefault;
        }

        $data = ['status' => 'error', 'message' => $message];

        if ($items) {
            foreach ($items as $key => $item) {
                $data['errors'][$key] = $item;
            }
        }

        return response()->json($data, $status);
    }

    /**
     * @param $message
     * @param null $items
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($message, $items = null, $status = 200)
    {
        if(is_null($message)) {
            $message = $this->messageSuccessDefault;
        }

        $data = ['status' => 'success', 'message' => $message];

        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }

        if ($items) {
            foreach ($items as $key => $item) {
                $data[$key] = $item;
            }
        }

        return response()->json($data, $status);
    }

    /**
     * @param $permission
     */
    public function hasPermissionTo($permission)
    {
        if(!\Auth::user()->hasPermissionTo($permission)) {
            throw new UnauthorizedException(403, 'Você não tem permissão suficiente para executar essa ação');
        }
    }

    /**
     * @return mixed
     */
    public function getUserAuth()
    {
        return Auth::user();
    }
}
