<?php

namespace App\Models\Admin;

use App\Models\TreePrice;
use App\Models\UserTreeRelation;
use App\Models\WishList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @category	Model
 * @package		Tree
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */
class Tree extends Model
{
    use HasFactory;

    protected $fillable = [
        'plantation_status','state_id', 'city_id','type', 'name', 'age', 'slug', 'sku', 'area_id',  'main_image', 'quantity', 'description','price_info', 'created_by', 'updated_by', 'status','trash'
    ];
    // 'price', 'discount_price',
    protected $appends  =   ['main_image_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

    const SPONSOR_TREE      =   1;
    const ADOPT_TREE        =   2;

    //Relations
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(TreeImage::class, 'tree_id', 'id');
    }

    public function price()
    {
        return $this->hasMany(TreePrice::class, 'tree_id', 'id');
    }
    public function wishLists()
    {
        return $this->hasMany(WishList::class);
    }

    public function userRelations()
    {
        return $this->hasMany(UserTreeRelation::class, 'adopted_tree_id');
    }


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

    public static function scopeSponsor($query)
    {
        return $query->where('type', self::SPONSOR_TREE);
    }
    public static function scopeAdopt($query)
    {
        return $query->where('type', self::ADOPT_TREE)->where('adopted_status', 0);
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
