@props([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
    'help' => '',
    'type' => '',
    'id' => '',
])

<div class="{{ $class }}">
    <label for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    {!! Form::text($name, $value, [
        'class' => 'form-control datepicker' . (!$required ? ' datepicker-optional' : ''),
        'id' => $id,
        'placeholder' => $placeholder,
        'type' => $type,
        'required' => $required ? true : false,
    ]) !!}

    @if ($help)
        <small class="form-text text-muted">
            {{ $help }}
        </small>
    @endif
</div>
