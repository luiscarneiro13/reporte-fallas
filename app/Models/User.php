<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cedula',
        'address',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        $str = '';

        if ($this->cedula) {
            $str = $this->cedula . ' - ';
        }

        if ($this->name) {
            $str = $str . $this->name;
        }

        if ($this->phone) {
            $str = $str . ' - ' . $this->phone;
        }

        return $str;
    }

    public function adminlte_image()
    {
        return url($this->profile_photo_url);
    }

    public function adminlte_profile_url()
    {
        return url('user/profile');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch');
    }

    public function userBranches()
    {
        return $this->hasMany(UserBranch::class, 'user_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_users')->withTimestamps();
    }

    /**
     * Get the first employee ID associated with the user.
     * Assumes a user is primarily linked to one employee record.
     *
     * @return int|null
     */
    public function getEmployeeIdAttribute()
    {
        // El método first() devuelve el primer modelo Employee o null
        // Luego usamos el operador de acceso nulo "?->" para obtener el 'id'
        return $this->employees->first()?->id;
    }
}
