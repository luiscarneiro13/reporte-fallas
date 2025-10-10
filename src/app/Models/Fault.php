<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fault extends Model
{
    use HasFactory;

    protected $table = "faults";

    protected $fillable = ['branch_id', 'description', 'service_area_id', 'fault_status_id', 'equipment_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceArea()
    {
        return $this->belongsTo(ServiceArea::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function faultStatus()
    {
        return $this->belongsTo(FaultStatus::class);
    }
}
