<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "users";

    protected $fillable = ['name', 'email', 'password', 'phone', 'email_verified_at'];

    public function branch()
    {
        return $this->belongsToMany(Branch::class, 'user_branch');
    }

    public function userBranches()
    {
        return $this->hasMany(UserBranch::class, 'user_id');
    }
}
