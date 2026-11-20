<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputerType extends Model
{
    protected $fillable = [
        'nama', 'tarif_per_jam',
    ];

    public function computers(){
        return $this->hasMany(Computer::class);
    }
}
