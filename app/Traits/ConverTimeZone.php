namespace App\Traits;

use Carbon\Carbon;

trait ConvertsTimezone
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/Caracas');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/Caracas');
    }
}
