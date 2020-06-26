<?php

namespace App\Services;

use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionService extends AbstractService
{
    /**
     * @var PermissionRepository
     */
    protected $repository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    public function __construct(
        PermissionRepository $permissionRepository,
        RoleRepository $roleRepository)
    {
        $this->repository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    public function assignPermission($params)
    {
        $input = $params['data'];
        $roles = $this->roleRepository->list();

        foreach ($input as $role_id => $data) {
            $role = \Spatie\Permission\Models\Role::findByName($roles[$role_id]);
            $permissions = array();
            foreach ($data as $permission_id => $value) {
                if ($value) {
                    $permissions[] = $permission_id;
                }
            }

            if ($role->name === $this->roleRepository->findByName('admin')->name) {
                $role->syncPermissions($this->repository->listByName());
            } else {
                $role->syncPermissions($this->repository->listByName($permissions));
            }
        }
    }

    /**
     * Used to fetch Pre-Requisites during Permission Assign
     * @get ("/api/permission/assign/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $arr['permissions'] = $this->repository->all();
        $arr['roles'] = $this->roleRepository->all();

        $assigned_permissions = \DB::table('role_has_permissions')->get();

        foreach ($arr['permissions'] as $permission) {
            foreach ($arr['roles'] as $role) {
                $arr['data'][$role->id][$permission->id] = $assigned_permissions->where('role_id', $role->id)->where('permission_id', $permission->id)->count() ? true : false;
            }
        }
        return $arr;
    }
}
