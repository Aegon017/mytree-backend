<?php

namespace App\Models;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreePlantation extends Model
{
    use HasFactory;
    protected $fillable = [
        'supervisor_id', 'tree_id','order_id', 'latitude', 'longitude', 'geoId', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status'
    ];

    // Relationship with the Supervisor (User)
    public function supervisor()
    {
        return $this->belongsTo(Admin::class, 'supervisor_id');
    }

    // Relationship with the Tree Plantation Images
    public function images()
    {
        return $this->hasMany(TreePlantationImage::class, 'tree_plantation_id', 'tree_id');
    }

    /**
     * Get the original tree associated with the tree relation.
     */
    // public function plantationDetails()
    // {
    //     return $this->hasMany(UserTreeRelation::class, 'adopted_tree_id', 'tree_id');
    // }
}

