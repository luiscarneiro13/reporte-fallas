<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
