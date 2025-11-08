<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory;

    protected $table = "equipment_types";

    protected $fillable = [
        'branch_id',
        'name',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
