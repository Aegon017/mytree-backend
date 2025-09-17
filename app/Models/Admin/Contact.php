<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @category	Model
 * @package		Contact
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on	03-09-2022
 */

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'mobile', 'email', 'subject', 'message', 'created_by', 'updated_by',
    ];

    const TRASH_ENABLE      =   1;
    const TRASH_DISABLE     =   0;

    /*
     * Scopes
     */
    public static function scopeTrashed($query)
    {
        return $query->where('trash', self::TRASH_ENABLE);
    }
    public static function scopeNotTrashed($query)
    {
        return $query->where('trash', self::TRASH_DISABLE);
    }
}
