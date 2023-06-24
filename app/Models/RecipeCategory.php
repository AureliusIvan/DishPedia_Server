<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    protected $table = 'recipe_category';
    protected $fillable = [
        'recipe_id', 'category_id',
    ];
    
    public $timestamps = false;
}