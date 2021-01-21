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
        return Carbon::createFromTimeStamp(strtotime($this->data))->format('d/M');
    }
}
