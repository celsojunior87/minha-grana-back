<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTransferencia extends Model
{
    public $table = 'item_transferencia';

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
