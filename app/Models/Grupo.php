<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Grupo extends Model
{
    public $table = 'grupo';

    public function items()
    {
        return $this->hasMany(Item::class, 'grupo_id', 'id');
    }

    /**
     * @param $queryBuilder
     * @param array $params
     * @return mixed
     */
    public function scopeQuery($queryBuilder, array $params)
    {

        if (Arr::has($params, 'date')) {
            $date = Arr::get($params, 'date');
            $newDate = Carbon::createFromFormat('Y-m', $date);
            $queryBuilder
                ->whereBetween('data',
                    [$newDate->firstOfMonth()->format('Y-m-d'), $newDate->lastOfMonth()->format('Y-m-d')]
                );
        }
        return $queryBuilder;
    }

    public function scopeQueryMovimentacao($queryBuilder, array $params)
    {
        dd($queryBuilder);
        dd($params);
    }
}
