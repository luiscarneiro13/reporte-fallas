<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePaymentMixed extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'bolivares_efectivo', 'dolares_efectivo', 'pago_movil', 'biopago', 'punto_venta_venezuela', 'punto_venta_banesco'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
