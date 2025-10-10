<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultStatus extends Model
{
    use HasFactory;

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
