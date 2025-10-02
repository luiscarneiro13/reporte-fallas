<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $table = "owners";

    protected $fillable = ['first_name', 'last_name'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
