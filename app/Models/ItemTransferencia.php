<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTransferencia extends Model
{
    public $table = 'item_transferencia';
    protected $fillable = ['item_id_de', 'item_id_para', 'vl_transferencia'];

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }
}
