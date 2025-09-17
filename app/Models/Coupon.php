<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
       'code', 'type', 'discount_value', 'usage_limit', 'used_count', 'valid_from', 'valid_to', 'status', 'trash', 'created_at', 'updated_at'
    ];

    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

    /*
     * Scopes
     */

     public static function scopeActive($query)
     {
         return $query->where('status', self::STATUS_ENABLE);
     }
     public static function scopeInActive($query)
     {
         return $query->where('status', self::STATUS_DISABLE);
     }
 
 
     public static function scopeTrashed($query)
     {
         return $query->where('trash', self::TRASH_ENABLE);
     }
     public static function scopeNotTrashed($query)
     {
         return $query->where('trash', self::TRASH_DISABLE);
     }
 

    

    /**
     * Check if the coupon is valid
     */
    public function isValid()
    {
        $now = Carbon::now();
        return $this->status == self::STATUS_ENABLE && $this->trash == self::TRASH_DISABLE &&
            (!$this->valid_from || $now->gte(Carbon::parse($this->valid_from))) &&
            (!$this->valid_to || $now->lte(Carbon::parse($this->valid_to))) &&
            $this->used_count < $this->usage_limit;
    }
}
