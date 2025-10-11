<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";

    protected $fillable = ['branch_id', 'identification_number', 'first_name', 'last_name', 'phone_number', 'address'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'employee_user')->withTimestamps();
    }
}
