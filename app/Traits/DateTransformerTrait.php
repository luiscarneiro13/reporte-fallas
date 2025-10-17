<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateTransformerTrait
{
    /**
     * Transforma los campos de fecha en un array de datos del formato de entrada (d-m-Y) al formato de MySQL (Y-m-d) o NULL.
     *
     * @param array $validatedData El array de datos validados.
     * @param array $dateFields Los nombres de los campos de fecha a transformar (ej: ['report_date']).
     * @param string $inputFormat El formato esperado de la fecha de entrada (por defecto: d-m-Y).
     * @return array El array de datos modificado.
     */
    protected function transformDateFields(array $validatedData, array $dateFields, string $inputFormat = 'd-m-Y'): array
    {
        foreach ($dateFields as $field) {
            // Verificar si el campo existe en los datos y no está vacío
            if (!empty($validatedData[$field])) {
                try {
                    // Transformar la fecha del formato de entrada al formato de MySQL
                    $validatedData[$field] = Carbon::createFromFormat($inputFormat, $validatedData[$field])
                        ->toDateString();
                } catch (\Exception $e) {
                    // En caso de error de formato (aunque el FormRequest debería validarlo), forzar NULL
                    $validatedData[$field] = null;
                }
            } else {
                // Si no se envía o está vacía, forzar NULL
                $validatedData[$field] = null;
            }
        }
        return $validatedData;
    }
}
