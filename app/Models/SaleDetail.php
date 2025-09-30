<?php

namespace App\Models;

use App\Helpers\Operations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['product_service_id', 'sale_id', 'product', 'qty', 'price', 'rate', 'average_rate', 'sub_total', 'sub_total_bs', 'price_bs'];
    protected $appends = ['product_name', 'price_dolars', 'sub_total_dolars'];
    // protected $appends = ['product_name', 'price_bs', 'subtotal_bs'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function getProductNameAttribute()
    {
        $name = explode('-', $this->product);
        return $name[0];
    }

    public function getPriceDolarsAttribute()
    {
        return Operations::roundUp($this->price_bs / $this->rate);
    }

    public function getSubTotalDolarsAttribute()
    {
        return Operations::roundUp($this->qty * ($this->price_bs / $this->rate));
    }
}
