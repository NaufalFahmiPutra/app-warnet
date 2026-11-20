<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalSession extends Model
{
    protected $fillable = [
        'computer_id', 'member_id', 'user_id', 'waktu_mulai', 'waktu_selesai', 'durasi_menit', 'total_biaya', 'status',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function computer(){
        return $this->belongsTo(Computer::class);
    }
    public function member(){
        return $this->belongsTo(Member::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
