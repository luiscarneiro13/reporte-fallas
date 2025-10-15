<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'name', 'address', 'phone', 'email'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
