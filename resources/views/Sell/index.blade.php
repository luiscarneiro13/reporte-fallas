@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    @vite('resources/js/productsSell.js')
    <p></p>
@stop

@section('content')
    <div id="productsSell"></div>
@stop

@php
    if (env('APP_ENV') == 'local') {
        $impresora = env('IMPRESORA_LOCAL_NOTADESPACHO');
    } else {
        $impresora = env('IMPRESORA_PRODUCCION_NOTADESPACHO');
    }
@endphp

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
        window.customers = @json($customers);
        window.articles = @json($articles);
        window.services = @json($services);
        window.dailyRate = {{ session('dailyRate') }};
        window.averageRate = {{ session('averageRate') }};
        window.tax = {{ session('tax') }};
        window.methodPayments = @json($methodPayments);
        window.customerSelected = "";
        window.articlesSelected = "";
        window.servicesSelected = "";
        window.impresora = "{{ $impresora }}";
        // window.branchId = null;
        // window.customers =null;
        // window.articles =null;
        // window.dailyRate = null;
        // window.customerSelected = "";
        // window.articlesSelected = "";
    </script>
@stop
