@extends('adminlte::master') {{-- Hereda el head, scripts base y plugins de AdminLTE --}}

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

{{-- Define las clases para el <body>. Usamos una clase simple para impresión. --}}
@section('classes_body', 'print-page')

{{-- Permite agregar CSS específico para esta vista, incluyendo los estilos de impresión. --}}
@section('adminlte_css')
    @stack('css')
    @yield('css')

    {{-- Estilos de impresión (Tu CSS original) --}}
    <style>
        @media print {

            /* Ocultar elementos de UI si los hubiera */
            .btn-imprimir {
                display: none !important;
            }

            #tabla-a-imprimir,
            #tabla-a-imprimir * {
                visibility: visible;
                font-size: 10pt;
                /* Tamaño de fuente para impresión */
            }

            #tabla-a-imprimir {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
            }
        }
    </style>
@stop

{{-- Contenido del Body: Solo el contenido principal. --}}
@section('body')
    <div class="wrapper">
        {{-- Contenedor principal sin wrapper de AdminLTE para un diseño limpio --}}
        <div class="content-wrapper ml-0" style="min-height: 100vh !important;">
            <section class="content">
                {{-- 👈 ESTE es el slot principal de CONTENIDO --}}
                @yield('content')
            </section>
        </div>
    </div>
@stop

{{-- Permite agregar JS específico --}}
@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
