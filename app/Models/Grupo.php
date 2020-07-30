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
        return $this->hasMany(Item::class, 'grupo_id', 'id')->orderBy('ordenacao');
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
        if (Arr::has($params, 'tipo_grupo')) {
            $tipo_grupo = Arr::get($params, 'tipo_grupo');
            if($tipo_grupo === 'receita') {
                $tipo_grupo = TipoGrupo::RECEITAS;
            }
            if($tipo_grupo === 'despesa') {
                $tipo_grupo = TipoGrupo::DESPESAS;
            }
            $queryBuilder
                ->where('tipo_grupo_id', '=', $tipo_grupo);
        }
        $queryBuilder->userAuth();
        return $queryBuilder;
    }

    public function scopeUserAuth($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }
}
