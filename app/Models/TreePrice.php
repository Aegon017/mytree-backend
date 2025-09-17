<?php

namespace App\Models;

use App\Models\Admin\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreePrice extends Model
{
    use HasFactory;

    protected $fillable = ['tree_id', 'duration', 'price'];

    /**
     * Get the tree price (if any).
     */
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }

}
