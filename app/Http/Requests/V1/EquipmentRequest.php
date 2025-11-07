<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // 2. Campos Requeridos y Únicos (Máx. 20, Min. 3)
            'placa' => ['required', 'string', 'max:20', 'min:3', 'unique:equipment,placa'],

            // 3. Campos Requeridos con Longitud Máxima de 90 (Min. 3)
            'serial_niv' => ['nullable', 'string', 'max:90', 'min:3'],
            'body_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'chassis_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'engine_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'vehicle_model' => ['required', 'string', 'max:90', 'min:3'],
            'brand_name' => ['nullable', 'string', 'max:90', 'min:3'],

            // 4. Campos Requeridos con Longitud Máxima de 20 (Min. 3 para owner, validación especial para model_year)
            'owner' => ['nullable', 'string', 'max:20', 'min:3'],

            // 5. Campos Opcionales (Nullable y Min. 3)
            'internal_code' => ['nullable', 'string', 'max:20', 'min:3'],
            'color' => ['nullable', 'string', 'max:20', 'min:3'],
            'origin' => ['nullable', 'string', 'max:255', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            // MENSAJES ESPECÍFICOS POR CAMPO Y REGLA

            // branch_id (Sucursal)
            'branch_id.required' => 'La sucursal es obligatoria.',
            'branch_id.integer' => 'La sucursal debe ser un número entero.',
            'branch_id.exists' => 'La sucursal seleccionada no existe en el sistema.',

            // placa
            'placa.max' => 'La placa no debe exceder los 20 caracteres.',
            'placa.min' => 'La placa debe tener al menos 3 caracteres.',
            'placa.unique' => 'Ya existe un equipo registrado con esa Placa. Debe ser única.',

            // serial_niv
            'serial_niv.max' => 'El Serial/NIV no debe exceder los 90 caracteres.',
            'serial_niv.min' => 'El Serial/NIV debe tener al menos 3 caracteres.',

            // body_serial_number
            'body_serial_number.max' => 'El número de serie de la carrocería no debe exceder los 90 caracteres.',
            'body_serial_number.min' => 'El número de serie de la carrocería debe tener al menos 3 caracteres.',

            // chassis_serial_number
            'chassis_serial_number.max' => 'El número de serie del chasis no debe exceder los 90 caracteres.',
            'chassis_serial_number.min' => 'El número de serie del chasis debe tener al menos 3 caracteres.',

            // engine_serial_number
            'engine_serial_number.max' => 'El número de serie del motor no debe exceder los 90 caracteres.',
            'engine_serial_number.min' => 'El número de serie del motor debe tener al menos 3 caracteres.',

            // vehicle_model
            'vehicle_model.max' => 'El modelo del vehículo no debe exceder los 90 caracteres.',
            'vehicle_model.min' => 'El modelo del vehículo debe tener al menos 3 caracteres.',

            // brand_name
            'brand_name.max' => 'El nombre de la marca no debe exceder los 90 caracteres.',
            'brand_name.min' => 'El nombre de la marca debe tener al menos 3 caracteres.',

            // owner
            'owner.max' => 'El nombre del propietario no debe exceder los 20 caracteres.',
            'owner.min' => 'El nombre del propietario debe tener al menos 3 caracteres.',

            // model_year
            'model_year.date_format' => 'El Año del Modelo debe tener un formato de año válido (YYYY).',
            'model_year.numeric' => 'El Año del Modelo debe ser un número.',
            'model_year.min' => 'El Año del Modelo debe ser posterior a 1900.',
            'model_year.max' => 'El Año del Modelo no puede ser posterior al año actual más uno.',

            // internal_code
            'internal_code.max' => 'El código interno no debe exceder los 20 caracteres.',
            'internal_code.min' => 'El código interno debe tener al menos 3 caracteres.',

            // color
            'color.max' => 'El campo Color no debe exceder los 20 caracteres.',
            'color.min' => 'El campo Color debe tener al menos 3 caracteres.',

            // origin
            'origin.min' => 'El campo Origen debe tener al menos 3 caracteres.',

            // racda
            'racda.min' => 'El campo RACDA debe tener al menos 3 caracteres.',

            // MENSAJES GENÉRICOS (Fallbacks)
            'required' => 'El campo :attribute es obligatorio.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        ];
    }
}
