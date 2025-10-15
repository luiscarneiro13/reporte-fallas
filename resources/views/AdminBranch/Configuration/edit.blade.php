@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Configuración</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-body">
            <form action="{{ route('admin.sucursal.configuration.update') }}" method="POST">
                @csrf
                <div class="row">
                    <input type="hidden" name="id" value="{{ $configuration->id }}">
                    <x-adminlte-input name="tax" label="% Impuesto" fgroup-class="col-md-3"
                        value="{{ $configuration->tax }}" />
                    <x-adminlte-input name="discount" label="Descuento global" fgroup-class="col-md-3"
                        value="{{ $configuration->discount }}" />
                </div>

                <div class="row mt-5">
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop
@section('customjs')

@stop
