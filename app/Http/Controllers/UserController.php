<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarRequest;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use http\Message;
use Illuminate\Http\Request;

class UserController extends AbstractController
{
    protected $service;
    protected $requestValidate = UserRequest::class;
//

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    /**
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function show($id)
    {
        return $this->ok($this->service->fetchUser($id));
    }

    /**
     * Atualiza avatar de usuário
     * @param AvatarRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatar(AvatarRequest $request, $id)
    {
        try {
            $avatar = $this->service->uploadAvatar($request->all(), $id);
            return $this->success('Foto atualizada com sucesso', ['avatar' => $avatar]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Cadastro externo de usuário
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cadastrar(Request $request)
    {

        try {
            $user = $this->service->cadastrar($request->all());
            return $this->success('Cadastro efetuado com sucesso', ['user' => $user]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function recuperarSenha(Request $request)
    {
        dd($request->all());
    }
}
