<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUser extends Model
{
    use HasFactory;

    protected $table = 'employee_users';

    protected $fillable = [
        'employee_id',
        'user_id',
        // campos adicionales si los necesitas:
        // 'assigned_by',
        // 'linked_at',
    ];

    /**
     * Si quieres habilitar timestamps automáticos (created_at, updated_at)
     */
    public $timestamps = true;

    // Relaciones opcionales:
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
