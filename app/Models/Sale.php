<?php

namespace App\Models;

use App\Helpers\Operations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'method_payment_id', 'branch_id', 'rate', 'average_rate', 'total', 'service', 'total_items', 'paid', 'total_bs'];

    protected $with = ['branch', 'methodPayment', 'paymentMixed', 'customer', 'details', 'invoices', 'deliveryNotes'];

    protected $appends = ['uuid', 'total_bolivares', 'total_dolares', 'totales', 'updated_at_fecha', 'updated_at_hora', 'created_at_fecha', 'created_at_hora'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function methodPayment()
    {
        return $this->belongsTo(MethodPayment::class, 'method_payment_id');
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'sale_id');
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class, 'sale_id');
    }

    public function paymentMixed()
    {
        return $this->hasOne(SalePaymentMixed::class, 'sale_id');
    }

    public function getUuidAttribute()
    {
        return str_pad($this->id, 8, "0", STR_PAD_LEFT);
    }

    public function getTotalesAttribute()
    {
        $methodPayments = MethodPayment::all()->toArray() ?? [];
        $methodPymentId = $this->methodPayment->id ?? null;
        $total = $this->total;
        $items = [];

        foreach ($methodPayments as $item) {
            if ($item['id'] == $methodPymentId) {
                $items[] = [$item['name'] => $total];
            }
            // else {
            //     $items[] = [$item['name'] => '0.00'];
            // }
        }

        return $items;
    }

    public function getTotalBolivaresAttribute()
    {
        return Operations::roundUp($this->total * $this->rate);
    }

    public function getTotalDolaresAttribute()
    {
        return Operations::roundUp($this->total);
    }

    public function getCreatedAtFechaAttribute()
    {
        $fechaHora = Carbon::parse($this->created_at)->setTimezone('America/Caracas');
        return $fechaHora->format('d-m-Y');
    }

    public function getCreatedAtHoraAttribute()
    {
        $fechaHora = Carbon::parse($this->created_at)->setTimezone('America/Caracas');
        return $fechaHora->format('h:i a');
    }

    public function getUpdatedAtFechaAttribute()
    {
        $fechaHora = Carbon::parse($this->updated_at)->setTimezone('America/Caracas');
        return $fechaHora->format('d-m-Y');
    }

    public function getUpdatedAtHoraAttribute()
    {
        $fechaHora = Carbon::parse($this->updated_at)->setTimezone('America/Caracas');
        return $fechaHora->format('h:i a');
    }
}
