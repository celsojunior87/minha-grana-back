<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    public $table = 'grupo';

    public function item()
    {
        return $this->hasOne(Grupo::class, 'id', 'id', 'grupo_item');
    }
}
