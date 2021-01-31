<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ItemMovimentacao extends Model
{
   public $table = 'item_movimentacao';
   public $fillable = ['item_id'];
   protected $primaryKey = 'id';

   protected $appends = ['data_formatted'];

   public function item()
   {
       return $this->belongsTo(Item::class,'item_id','id');
   }

    public function getDataFormattedAttribute()
    {
        date_default_timezone_set('America/Sao_Paulo');
        setlocale(LC_ALL, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
        setlocale(LC_TIME, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
        $data = Carbon::createFromTimeStamp(strtotime($this->data));
        $mes = ucfirst($data->locale('pt')->shortMonthName);
        return $data->day . '/' . $mes;
    }
}
