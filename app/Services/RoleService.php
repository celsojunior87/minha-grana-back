<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService extends AbstractService
{
    /**
     * @var RoleRepository
     */
    protected $repository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->repository = $roleRepository;
    }

    public function validadeOnDelete(int $id)
    {
        $role = $this->repository->find($id);
        if ($role->name == 'admin') {
            throw new \Exception('O perfil Administrador não pode ser excluído');
        }
        if ($role->name == 'super') {
            throw new \Exception('O perfil Super Administrador não pode ser excluído');
        }
    }
}
