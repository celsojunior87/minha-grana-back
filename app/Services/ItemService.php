<?php


namespace App\Services;


use App\Repositories\ItemRepository;

class ItemService extends AbstractService
{
    protected $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;

    }
}
