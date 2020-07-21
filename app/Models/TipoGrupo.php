<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGrupo extends Model
{
    public $table = 'tipo_grupo';
    public $timestamps = false;

    const RECEITAS = 1;
    const DESPESAS = 2;
}
