<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository extends RepositoryInterface
{
    /**
     * @return mixed
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param $params
     * @param null $with
     * @return mixed
     */
    public function all($params = null, $with = [])
    {
        return $this->model->with($with)->query($params)->get();
    }

    /**
     * Retorna em forma de lista para selecte
     * @return mixed
     */
    public function list($sortBy = 'nome', $pluck = 'nome'): array
    {
        return $this->model->all()->sortBy($sortBy)->pluck($pluck, 'id')->all();
    }

    /**
     * @param $params
     * @return Model
     */
    public function create($params): Model
    {
        return $this->model->forceCreate($this->formatParams($params));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id, $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    /**
     * @param int $int
     * @return mixed
     */
    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findByUserAuth($params)
    {
        if (isset($params['id_user']) && !empty($params['id_user'])) {
            return $this->findOrFail($params['id_user']);
        }
        /**
         * @todo achar o usuario, mas se nao existir um id_user, retorna o usuario logado
         */
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    /**
     * @param $entity
     * @param $data
     */
    public function update(Model $entity, $data)
    {
        $entity->forceFill($this->formatParams($data))->save();
        return $this->find($entity->id);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function formatParams($params)
    {
        return $params;
    }
}
