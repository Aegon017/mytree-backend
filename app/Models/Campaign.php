<?php

namespace App\Models;

use App\Models\Admin\City;
use App\Models\Admin\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id', 'city_id', 'name', 'age', 'slug', 'sku', 'area', 'goal_amount', 'raised_amount', 'main_image', 'expiration_date','description', 'created_by', 'updated_by', 'status','trash'
    ];
    protected $appends  =   ['main_image_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

    //Relations
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
    // public function images()
    // {
    //     return $this->hasMany(TreeImage::class, 'tree_id', 'id');
    // }


    /*
     * Attributes
     */
    public function getMainImageUrlAttribute()
    {
        return $this->main_image ?
            url(env('TREE_UPLOAD_PATH') . $this->main_image) :
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
