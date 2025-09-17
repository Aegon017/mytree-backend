<?php

namespace App\Models;

use App\Models\Admin\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="CartItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="quantity", type="integer"),
 *     @OA\Property(property="product", type="object", ref="#/components/schemas/Product")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 * )
 */

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['cart_type','product_type','location_id','user_id','type','product_id', 'quantity','duration', 'name', 'occasion', 'message'];

    /**
     *Relations.
     */
    // public function product()
    // {
    //     return $this->belongsTo(Tree::class);
    // }
    

    // /**
    //  * Dynamically determine the product relationship based on product_type.
    //  */
    // public function product()
    // {
    //     return $this->morphTo(null, 'product_type', 'product_id');
    // }

    /**
     * Relationship with Tree model when product_type = 1.
     */
    public function product()
    {
        return $this->belongsTo(Tree::class, 'product_id');
    }

    /**
     * Relationship with Product model when product_type = 2.
     */
    public function ecomProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    
    public function location()
    {
        return $this->belongsTo(TreeLocation::class, 'location_id');
    }
}
