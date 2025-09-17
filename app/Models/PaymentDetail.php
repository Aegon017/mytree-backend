<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'razorpay_payment_id',
        'entity',
        'amount',
        'currency',
        'status',
        'razorpay_order_id',
        'invoice_id',
        'international',
        'method',
        'amount_refunded',
        'refund_status',
        'captured',
        'description',
        'card_id',
        'bank',
        'wallet',
        'vpa',
        'email',
        'contact',
        'notes',
        'fee',
        'tax',
        'error_code',
        'error_description',
        'pay_created_at',
    ];
}
