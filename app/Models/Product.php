<?php

namespace App\Models;

use App\Models\Admin\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'botanical_name', 'nick_name', 'slug', 'sku', 'price', 'main_image', 'quantity', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status' // 'discount_price',
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
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    
    // public function wishLists()
    // {
    //     return $this->hasMany(WishList::class);
    // }
    public function wishLists()
    {
        return $this->hasMany(WishList::class, 'tree_id', 'id');
    }
    public function getWishlistTagAttribute()
    {
        // Assuming the current authenticated user
        $user = auth()->user();
        
        // Return true if the product is in the user's wishlist
        return $this->wishLists()->where('user_id', $user->id)->exists();
    }


    /*
     * Attributes
     */
    public function getMainImageUrlAttribute()
    {
        return $this->main_image ?
            url(env('PRODUCT_UPLOAD_PATH') . $this->main_image) :
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
