<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $table = 'item';

    public $fillable = ['ordenacao'];

    public function grupo()
    {
        return $this->hasMany(Grupo::class,'id','grupo_id');
    }

    public function itemMovimentacao()
    {
        return $this->hasMany(ItemMovimentacao::class,'item_id','id');
    }
}
