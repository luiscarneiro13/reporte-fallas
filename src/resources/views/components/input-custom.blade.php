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
    <label for="{{ $name }}">{{ $label }} @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $id }}" class="form-control"
        placeholder="{{ $placeholder }}" value="{{ $value }}">

    {{-- Texto de ayuda (small) --}}
    @if ($help)
        <small class="form-text text-muted">
            {{ $help }}
        </small>
    @endif
</div>
