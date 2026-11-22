<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Computer extends Model
{
    protected $fillable = [
        'nama_pc', 'computer_type_id', 'spesifikasi', 'status',
    ];

    public function computerType(){
        return $this->belongsTo(ComputerType::class);
    }

    public function rentalSessions(){
        return $this->hasMany(RentalSession::class);
    }
}
