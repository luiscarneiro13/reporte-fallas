@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar la tasa actual</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-body">
            <form action="{{ route('admin.sucursal.daily.rate.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-input name="rate" label="Tasa BCV" value="{{ session('dailyRate') }}">
                            <x-slot name="bottomSlot">
                                <small>Ej: 35.99</small>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="average_rate" label="Tasa Promedio" value="{{ session('averageRate') }}">
                            <x-slot name="bottomSlot">
                                <small>Ej: 35.99</small>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
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
