<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarRequest;
use App\Http\Requests\UserRequest;
use App\Services\UserService;

class UserController extends AbstractController
{
    protected $service;
    protected $with = ['roles'];
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
}
