<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public $table = 'status';
    public $timestamps = false;

    CONST AJUSTE = 1;
    CONST FEITO = 2;
    CONST AGUARDANDO = 3;
    CONST VAZIO = '';
}
