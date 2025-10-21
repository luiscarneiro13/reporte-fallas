@props(['label', 'value', 'class'])

<div class="{{ $class }}">
    {{-- 1. Forzamos al LABEL contenedor a ser font-normal para anular AdminLTE/Bootstrap. --}}
    <span class="mt-3 font-normal" {{ $attributes->merge(['class' => '']) }}>

        {{-- 2. Hacemos el LABEL explícitamente negrita usando STRONG y font-bold --}}
        <strong class="font-bold">{{ $label }}: </strong>

        {{-- 3. El Valor hereda el peso normal del LABEL padre y se ve sin negrita --}}
        <span>{{ $value }}</span>

    </span>
</div>
