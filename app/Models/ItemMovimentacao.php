<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMovimentacao extends Model
{
   public $table = 'item_movimentacao';


   public function item()
   {
       return $this->hasMany(Item::class,'id','item_id');
   }
}
