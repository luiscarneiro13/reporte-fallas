<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = "branches";

    protected $fillable = ['name', 'description', 'phone', 'email', 'logo', 'rif', 'address'];

    public function brands()
    {
        return $this->hasMany(Brand::class, 'branch_id');
    }

    public function typeArticles()
    {
        return $this->hasMany(TypeArticle::class, 'branch_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_branch');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'branch_id');
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'user_branch');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'branch_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'branch_id');
    }

    public function serviceAreas()
    {
        return $this->hasMany(ServiceArea::class, 'branch_id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'branch_id');
    }
}
