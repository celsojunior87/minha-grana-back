<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoItem extends Model
{
    protected $table = 'tipo_item';
    public $timestamps = false;

    const ECONOMIA = 1;
    const DIVIDA = 2;
}
