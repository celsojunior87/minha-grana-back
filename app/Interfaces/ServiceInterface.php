<?php

namespace App\Interfaces;

abstract class ServiceInterface
{
    abstract public function validadeOnInsert($params);
    abstract public function validadeOnUpdate($id, $params);
    abstract public function validadeOnDelete(int $id);
    abstract public function afterSave($entity, $params);
    abstract public function afterUpdate($entity, $params);
}
