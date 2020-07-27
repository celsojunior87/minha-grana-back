<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $table = 'item';

    public $fillable = ['ordenacao'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id', 'id');
    }

    public function itemMovimentacao()
    {
        return $this->hasMany(ItemMovimentacao::class, 'item_id', 'id');
    }
}
