<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'nama', 'no_telp', 'saldo',
    ];

    public function rentalSessions(){
        return $this->hasMany(RentalSession::class);
    }
    public function topups(){
        return $this->hasMany(Topup::class);
    }
}
