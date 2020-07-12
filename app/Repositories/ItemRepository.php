<?php


namespace App\Repositories;

use App\Models\Item;

class ItemRepository extends AbstractRepository
{
    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
    }
}
