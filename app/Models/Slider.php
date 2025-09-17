<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','main_image','created_by', 'updated_by', 'status','trash'
    ];
    protected $appends  =   ['main_image_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;



    /*
     * Attributes
     */
    public function getMainImageUrlAttribute()
    {
        return $this->main_image ?
            url(env('SLIDER_UPLOAD_PATH') . $this->main_image) :
            "";
    }
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

    public static function scopeSlug($query, $keyword)
    {
        return $query->where('slug', $keyword);
    }
}
