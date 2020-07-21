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
        return $this->hasMany(Item::class, 'grupo_id', 'id')->orderBy('created_at');
    }

    public function tipoGrupo()
    {
        return $this->belongsTo(TipoGrupo::class, 'tipo_grupo_id', 'id');
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
                )
                ->orderBy('created_at');
        }
        $queryBuilder->userAuth();
        return $queryBuilder;
    }

    public function scopeUserAuth($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeQueryMovimentacao($queryBuilder, array $params)
    {
        if (Arr::has($params, 'date')) {
            $date = Arr::get($params, 'date');
            $newDate = Carbon::createFromFormat('Y-m', $date);
            $queryBuilder
                ->whereHas('items', function($query) {
                    return $query->where('created_at', '!=', 'updated_at');
                })
                ->whereBetween('data',
                    [$newDate->firstOfMonth()->format('Y-m-d'), $newDate->lastOfMonth()->format('Y-m-d')]
                );
        }
        $queryBuilder->userAuth();
        return $queryBuilder;
    }
}
