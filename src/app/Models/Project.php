<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = "projects";

    protected $fillable = ['branch_id', 'division_id', 'customer_id',  'name', 'description', 'geographic_area', 'contract_number'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
