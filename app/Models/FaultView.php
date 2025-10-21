<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FaultView extends Model
{
    use HasFactory;

    protected $table = 'v_faults_base';

    public $timestamps = false;

    protected $dates = [
        'report_date',
        'closed_at',        // f.closed AS closed_at
        'completed_execution', // f.completed_execution
    ];

    protected function humanDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                $days = $this->duration_days;

                if ($days === null) {
                    return 'N/A';
                } elseif ($days < 7) {
                    return "{$days} días";
                } elseif ($days < 30) {
                    $weeks = floor($days / 7);
                    return "{$weeks} semanas";
                } elseif ($days < 365) {
                    $months = floor($days / 30.4375); // Aproximación
                    return "{$months} meses";
                } else {
                    $years = floor($days / 365.25); // Aproximación
                    return "{$years} años";
                }
            }
        );
    }
}
