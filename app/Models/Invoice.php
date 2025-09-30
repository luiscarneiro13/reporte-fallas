<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rif', 'address', 'sub_total', 'tax', 'total', 'sale_id', 'rate', 'average_rate', 'branch_id'];

    protected $appends = ['uuid', 'updated_at_fecha', 'updated_at_hora', 'created_at_fecha', 'created_at_hora'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function getUuidAttribute()
    {
        return str_pad($this->id, 8, "0", STR_PAD_LEFT);
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
