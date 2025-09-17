<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @category	Model
 * @package		Admin
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

class Admin  extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    // use Notifiable;
    protected $guard = "admin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','emp_ref', 'email', 'password','mobile','image','role','created_by', 'updated_by', 'status','login_at','logout_at','latitude','longitude'
    ];
    protected $appends  =   ['image_url'];
    const STATUS_ENABLE     =   1;
    const STATUS_DISABLE    =   0;
    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     /*
     * Attributes
     */
    public function getImageUrlAttribute()
    {
        return $this->image ?
            url('/uploads/employee/' . $this->image) :
            "";
    }

    /*
     * Scopes
     */

     public static function scopeSupervisor($query)
     {
         return $query->where('role', 2);
     }

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
}
