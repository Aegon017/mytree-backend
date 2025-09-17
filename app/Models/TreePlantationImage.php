<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreePlantationImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tree_plantation_id',
        'image'
    ];

    protected $appends  =   ['image_url'];

     /*
     * Attributes
     */
    public function getImageUrlAttribute()
    {
        return $this->image ?
            url(env('TREE_PLANTATION_PATH') . $this->image) :
            "";
    }

    // Relationship with the Tree Plantation
    public function treePlantation()
    {
        return $this->belongsTo(TreePlantation::class);
    }
}
