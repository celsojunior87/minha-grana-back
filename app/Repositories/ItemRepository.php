<?php


namespace App\Repositories;



use App\Models\Item;

class ItemRepository
{
    protected $model;
    public function __construct(Item $model)
    {
        $this->model = $model;
    }
}