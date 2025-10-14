<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    use HasFactory;

    protected $table = "service_areas";

    protected $fillable = ['name', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function faults()
    {
        return $this->hasMany(Fault::class);
    }
}
