<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = "projects";

    protected $fillable = ['branch_id', 'customer_id', 'division_id', 'name', 'contract_number', 'description', 'geographic_area' ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
