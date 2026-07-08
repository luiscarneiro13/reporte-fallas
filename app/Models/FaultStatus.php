<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultStatus extends Model
{
    use HasFactory;

    /**
     * Nombre del estatus fijo que se asigna automáticamente a las fallas
     * reportadas por usuarios con rol Operador.
     */
    const OPERATOR_STATUS_NAME = 'Por programación interna';

    protected $table = "fault_statuses";

    protected $fillable = ['name'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function faults()
    {
        return $this->hasMany(Fault::class);
    }
}
