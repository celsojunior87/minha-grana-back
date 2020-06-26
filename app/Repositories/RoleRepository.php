<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends AbstractRepository
{
    protected $model;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * @return array
     */
    public function list($sortBy = 'nome', $pluck = 'nome'): array
    {
        return $this->model->all()->pluck('name', 'id')->all();
    }

    /**
     * Get role by name
     *
     * @return Role
     */

    public function findByName($name = null)
    {
        return $this->model->filterByName($name)->first();
    }

    /**
     * List (name,id) all roles by name where given name is not included
     *
     * @return Role
     */

    public function listExceptName($names = array())
    {
        return $this->model->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List (name) all roles by id
     *
     * @return Role
     */

    public function listNameById($ids = array())
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        return $this->model->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all names
     *
     * @return Role
     */

    public function listName()
    {
        return $this->model->all()->pluck('name')->all();
    }

    /**
     * @param $params
     * @return array
     */
    public function formatParams($params)
    {
        $formatted = [
            'name' => $params['name'],
            'public_name' => $params['public_name'],
            'guard_name' => 'api'
        ];
        return $formatted;
    }
}
