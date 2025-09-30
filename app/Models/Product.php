<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'name', 'description', 'brand_id', 'type_article_id', 'available_qty', 'price'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function typeArticle()
    {
        return $this->belongsTo(TypeArticle::class, 'type_article_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function modelVehicles()
    {
        return $this->belongsToMany(ModelVehicle::class, 'product_model_vehicles', 'product_id', 'model_vehicle_id');
    }

    public function productEntry()
    {
        return $this->hasMany(ProductEntry::class, 'product_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'product_id');
    }
}
