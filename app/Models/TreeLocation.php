<?php

namespace App\Models;

use App\Models\Admin\Area;
use App\Models\Admin\City;
use App\Models\Admin\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeLocation extends Model
{
    use HasFactory;
    protected $fillable = ['state_id', 'city_id', 'area_id'];

    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;


    /*
     * Attributes
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
     *Relations.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
