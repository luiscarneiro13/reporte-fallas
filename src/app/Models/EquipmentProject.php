<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentProject extends Model
{
    use HasFactory;

    protected $table = "equipment_project";

    protected $fillable = ['equipment_id', 'project_id'];
}
