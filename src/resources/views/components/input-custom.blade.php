@props([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
])

<x-adminlte-input name="{{ $name }}" placeholder="" fgroup-class="{{ $class }}" value="{{ $value }}">
    {{-- ✅ Pasa la etiqueta usando el slot, permitiendo HTML --}}
    <x-slot name="label">
        {{ $label }} @if ($required)
            <span class="text-danger">*</span>
        @endif
    </x-slot>
</x-adminlte-input>
