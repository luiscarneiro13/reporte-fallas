@props([
    'name', // El texto que se mostrará en la etiqueta (ej: 'Cerrada')
    'type' => 'secondary', // El color/tipo (primary, success, danger, etc.)
    'pill' => true, // Controla si se usa el estilo 'badge-pill' para ser más redondeado
])

@php
    // Normalizar el tipo a una clase de color válida para AdminLTE/Bootstrap.
    $validTypes = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
    $colorClass = in_array($type, $validTypes) ? $type : 'secondary';

    // Clase de forma: 'badge-pill' si pill es true.
    $shapeClass = $pill ? 'badge-pill' : '';

    // Clases base de Bootstrap/AdminLTE para Badges
    $baseClasses = "badge badge-{$colorClass} {$shapeClass}";

@endphp

<!-- Usamos un <span> para mostrar la etiqueta de estado de Bootstrap -->

<span {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $name }}
</span>
