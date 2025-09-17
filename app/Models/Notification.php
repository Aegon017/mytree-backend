<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'message', 'send_to', 'user_ids'
    ];

    protected $casts = [
        'user_ids' => 'array',
    ];
}
