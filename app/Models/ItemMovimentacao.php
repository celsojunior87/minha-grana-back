<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMovimentacao extends Model
{
   public $table = 'item_movimentacao';
   public $fillable = ['item_id'];
   protected $primaryKey = 'id';

   public function item()
   {
       return $this->belongsTo(Item::class,'item_id','id');
   }
}
