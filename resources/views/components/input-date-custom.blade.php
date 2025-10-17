@props([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
    'help' => '',
    'type' => 'text', // Aseguramos que sea 'text' por defecto
    'id' => '',
])

@php
    // Si el valor existe, intentamos convertirlo del formato Y-m-d (BD) al formato d-m-Y (Input)
    if ($value) {
        try {
            // Se usa \Carbon\Carbon::parse para manejar posibles valores de tipo Carbon\Carbon o string.
            // Se asume el formato de entrada de la BD es Y-m-d, y se formatea para la vista a d-m-Y.
            $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable $th) {
            // Si hay un error de parseo (ej: el valor no es una fecha válida), se deja como está para evitar errores.
        }
    }
@endphp

<div class="{{ $class }}">
    <label for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    {!! Form::text($name, $value, [
        // 1. Clase 'datepicker' siempre debe estar para que JS lo detecte.
        // 2. Si NO es requerido, se añade 'datepicker-optional'.
        'class' => 'form-control datepicker' . (!$required ? ' datepicker-optional' : ''),
        'id' => $id,
        'placeholder' => $placeholder,
        'type' => $type,
        'required' => $required, // Blade ya maneja los booleanos si solo pasas $required
    ]) !!}

    @if ($help)
        <small class="form-text text-muted">
            {{ $help }}
        </small>
    @endif
</div>
