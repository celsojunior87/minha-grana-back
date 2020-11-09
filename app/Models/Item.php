<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $table = 'item';

    public $fillable = ['ordenacao'];
    protected $appends = ['vl_saldo_final'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id', 'id');
    }

    public function itemMovimentacao()
    {
        return $this->hasMany(ItemMovimentacao::class, 'item_id', 'id');
    }

    /**
     * saldo final (não editavel)
     * (Saldo inicial + Planejado desse mês ) -
     * (gasto nesse mês) (disabled sempre )
     */
    public function getVlSaldoFinalAttribute()
    {
        $result = ($this->vl_saldo_inicial + $this->vl_esperado) - $this->vl_gasto;
        return $result;
    }
}
