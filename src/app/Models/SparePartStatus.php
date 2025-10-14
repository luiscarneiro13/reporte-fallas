<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePartStatus extends Model
{
    use HasFactory;

    protected $table = "spare_part_statuses";

    protected $fillable = ['branch_id', 'name'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function faults()
    {
        return $this->hasMany(Fault::class);
    }
}
