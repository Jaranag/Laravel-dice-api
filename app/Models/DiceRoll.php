<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiceRoll extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'id_user',
        'id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }


}
