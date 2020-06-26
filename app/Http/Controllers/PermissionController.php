<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Parent_;

class PermissionController extends AbstractController
{
    protected $service;
    protected $requestValidate = PermissionRequest::class;

    /**
     * PermissionController constructor.
     * @param PermissionService $service
     */
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * Used to assign Permission
     * @post ("/api/permission/assign")
     * @param ({
     *      @Parameter("data", type="array", required="true", description="Array of Permissions"),
     * })
     * @return Response
     */
    public function assignPermission(Request $request)
    {
        try {
            $this->service->assignPermission($request->all());
            return $this->success('Permissões atribuídas com sucesso');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Used to store Permission
     * @post ("/api/permission")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Permission"),
     * })
     * @return Response
     */
    public function store(Request $request)
    {
        return parent::store($request);
    }
}
