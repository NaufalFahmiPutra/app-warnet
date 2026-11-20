<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $fillable = [
        'member_id', 'user_id', 'nominal',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
