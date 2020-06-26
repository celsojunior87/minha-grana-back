<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $request;
    protected $userRepository;
    protected $module = 'user';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, UserRepository $user)
    {
        $this->request = $request;
        $this->userRepository = $user;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function me(Request $request)
    {
        return $this->ok($this->userRepository->check($request));
    }
}
