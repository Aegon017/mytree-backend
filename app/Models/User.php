<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'profile',
        'user_type',
        'mobile_prefix',
        'referral_code',
        'referred_by',
        'fcm_token'
    ];
    protected $appends  =   ['profile_image_url'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->referral_code = strtoupper(substr(md5($user->id . $user->email), 0, 10));
            $user->save();
        });
    }

    /*
     * Attributes
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profile ?
            url(env('USER_PROFILE_PATH') . $this->profile) :
            "";
    }

    /*
     * Relations
     */

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function refered_user()
    {
        return $this->belongsTo(User::class, 'referred_by', 'id');
    }

    public function wishLists()
    {
        return $this->hasMany(WishList::class);
    }


    public function subscriptions()
    {
        return $this->hasMany(UserTreeRelation::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
}
