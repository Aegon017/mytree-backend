<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'main_img', 'created_by', 'updated_by', 'status'
    ];
    protected $appends  =   ['main_img_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

 

    /*
     * Attributes
     */
    public function getMainImgUrlAttribute()
    {
        return $this->main_img ?
            url('/uploads/locations/' . $this->main_img) :
            "";
    }
    

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

    
    public static function scopeSlug($query, $keyword)
    {
        return $query->where('slug', $keyword);
    }
}
