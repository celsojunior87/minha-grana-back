<?php


namespace App\Services;

use App\Interfaces\ServiceInterface;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;

abstract class AbstractService extends ServiceInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function getAll($params = null, $with = null)
    {
        return $this->repository->all($params, $with);
    }

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function find(int $id, $with = [])
    {
        $result =  $this->repository->find($id, $with);
        if($result == null){
            throw new \Exception('Objeto não encontrado na base de dados');
        }
        return $result;

    }

    /**
     * @param array $data
     * @return array
     */
    public function beforeSave(array $data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        $data = $this->beforeSave($data);
        if ($this->validadeOnInsert($data) !== false) {
            $entity = $this->repository->create($data);
            $this->afterSave($entity, $data);
            return $entity;
        }
    }

    public function afterSave($entity, $params)
    {
        return $entity;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function update($id, $data)
    {
        $this->validadeOnUpdate($id, $data);
        $entity = $this->find($id);
        $this->afterUpdate($entity, $data);
        return $this->repository->update($entity, $data);
    }

    public function afterUpdate($entity, $params)
    {

    }

    public function beforeDelete($id)
    {
        return $id;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $this->validadeOnDelete($id);
        $this->beforeDelete($id);
        $this->repository->delete($id);
        $this->afterDelete($id);
        return $id;
    }

    public function afterDelete($entity)
    {
        return $entity;
    }

    /**
     * @param bool $withGenerateSelectOption
     * @return array
     */
    public function toSelect($withGenerateSelectOption = true)
    {
        if ($withGenerateSelectOption) {
            return generateSelectOption($this->repository->list());
        }
        return $this->repository->list();
    }

    /**
     * @param $params
     * @return bool
     */
    public function validadeOnInsert($params)
    {

    }

    /**
     * Validação ao atualizar um registro
     * @param $id
     * @param $params
     */
    public function validadeOnUpdate($id, $params)
    {

    }

    /**
     * Validação ao deletar um registro
     * @param int $id
     */
    public function validadeOnDelete(int $id)
    {
        $result =  $this->repository->find($id);
        if($result == null){
            throw new \Exception('Objeto não encontrado na base de dados');
        }

    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getUserAuth()
    {
        return Auth::user();
    }
}
