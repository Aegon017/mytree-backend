<?php

namespace App\Models\Admin;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;


    protected $fillable = [
        'name','slug','icon', 'created_by', 'updated_by', 'status'
    ];
    protected $appends  =   ['image_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

   
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /*
     * Attributes
     */
    public function getImageUrlAttribute()
    {
        return $this->icon ?
            url('/uploads/category/' . $this->icon) :
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
