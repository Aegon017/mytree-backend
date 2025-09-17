<?php

namespace App\Models;

use App\Models\Admin\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTreeRelation extends Model
{
    use HasFactory;

    protected $table = 'user_tree_relations'; // Table name
    protected $fillable = [
        'user_id',
        'order_id',
        'original_tree_id',
        'adopted_tree_id',
        'subscription_start',
        'subscription_end',
        'status',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    /**
     * Get the user associated with the tree relation.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the original tree associated with the tree relation.
     */
    public function originalTree()
    {
        return $this->belongsTo(Tree::class, 'original_tree_id');
    }

    /**
     * Get the adopted tree associated with the tree relation.
     */
    public function adoptedTree()
    {
        return $this->belongsTo(Tree::class, 'adopted_tree_id');
    }

    public function orderAssignments()
    {
        return $this->hasMany(OrderAssignment::class, 'order_id', 'order_id');
    }

    public function plantationDetails()
    {
        return $this->belongsTo(TreePlantation::class, 'adopted_tree_id', 'tree_id');
    }
}

