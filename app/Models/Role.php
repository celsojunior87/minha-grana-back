<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 1;
    const CLIENTE = 2;

    protected $fillable = [
        'name'
    ];
    protected $primaryKey = 'id';
    protected $table = 'roles';

    public function getDetailAttribute()
    {
        return ucfirst($this->name);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeFilterByName($q, $name = null)
    {
        if (!$name) {
            return $q;
        }
        return $q->where('name', 'like', '%' . $name . '%');
    }
}
