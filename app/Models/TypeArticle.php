<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeArticle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

        public function products()
    {
        return $this->hasMany(Product::class, 'type_article_id');
    }
}
