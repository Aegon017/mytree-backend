<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['shipping_address_id','cart_type','coupon_code','coupon_type','product_type','user_id','order_ref','tax_amount','amount','amount','amount','amount','amount', 'razorpay_order_id', 'razorpay_payment_id', 'order_status', 'payment_status', 'address', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status'];
    
    const SPONSER       =   1;
    const ADOPT         =   2;
    const ADOPT_RENEWAL =   3;

    const TREE_ORDERS           =   1;
    const ECOMMERCE_ORDERS      =   2;

    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id', 'id');
    }
   
   
    public function orderLogs()
    {
        return $this->hasMany(OrderLog::class);
    }
    public function paymentDetails()
    {
        return $this->hasOne(PaymentDetail::class);
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

     public static function scopeTreeOrders($query)
     {
         return $query->where('product_type', self::TREE_ORDERS);
     }
     public static function scopeEcommerceOrders($query)
     {
         return $query->where('product_type', self::ECOMMERCE_ORDERS);
     }
}
