<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'tree_id'
    ];
    protected $appends  =   ['image_url'];
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

    /*
     * Attributes
     */
    public function getImageUrlAttribute()
    {
        return $this->image ?
            url(env('TREE_UPLOAD_PATH') . $this->image) :
            "";
    }
}
