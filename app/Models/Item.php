<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $table = 'item';

    public function grupo()
    {
        return $this->hasMany(Grupo::class,'id','grupo_id');
    }
}
