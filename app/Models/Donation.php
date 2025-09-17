<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'donor_id',
        'donor_name',
        'donor_email',
        'amount',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
