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

<div class="mt-3 {{ $class }}">
    <label for="{{ $name }}">{{ $label }} @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <x-adminlte-textarea name="{{ $name }}" id="{{ $id }}" class="form-control"
        placeholder="{{ $placeholder }}">{{ $value }}</x-adminlte-textarea>
    {{-- Texto de ayuda (small) --}}
    @if ($help)
        <small class="form-text text-muted">
            {{ $help }}
        </small>
    @endif
</div>
