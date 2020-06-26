<?php
namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Validation\ValidationException;

class PermissionRepository extends AbstractRepository
{
    protected $model;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    /**
     * List all permissions by name
     *
     * @return Permission
     */
    public function listByName($ids = [])
    {
        if (count($ids)) {
            return $this->model->whereIn('id', $ids)->get()->pluck('name')->all();
        } else {
            return $this->model->all()->pluck('name')->all();
        }
    }

    /**
     * List all names
     *
     * @return Permission
     */
    public function listName()
    {
        return $this->model->all()->pluck('name')->all();
    }

    /**
     * Find permission with given id or throw an error.
     *
     * @param integer $id
     * @return Permission
     */
    public function findOrFail($id)
    {
        $permission = $this->model->find($id);

        if (! $permission) {
            throw ValidationException::withMessages(['message' => trans('permission.could_not_find')]);
        }

        return $permission;
    }

    /**
     * @param $params
     * @return array
     */
    public function formatParams($params)
    {
        $formatted = [
            'name' => $params['name'],
            'guard_name' => 'api'
        ];
        return $formatted;
    }
}
