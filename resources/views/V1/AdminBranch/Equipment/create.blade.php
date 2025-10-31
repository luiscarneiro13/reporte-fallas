@extends('adminlte::page')

@section('title', 'Equipos')

@section('content_header')

    <h1>Crear equipo</h1>

@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.equipment.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif

                <div class="row">
                    <x-input-custom name="internal_code" label="Código interno" placeholder="" class="col-md-3"
                        value="{{ old('internal_code') }}" />

                    <div class="col-md-5">
                        <x-label value="Proyecto" required />
                        {{ Form::select('project_id', $projects, null, ['class' => 'select2 form-control']) }}
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Datos físicos</h5>

                <div class="row">

                    <x-input-custom required name="placa" placeholder="" value="{{ old('placa') }}" label="Placa"
                        class="col-md-2" />

                    <x-input-custom required name="brand_name" placeholder="" value="{{ old('brand_name') }}" label="Marca"
                        class="col-md-2" />

                    <x-input-custom required name="vehicle_model" label="Modelo" placeholder=""
                        value="{{ old('vehicle_model') }}" class="col-md-2" />

                    <div class="col-md-2">
                        <x-label value="Año" required />
                        {{ Form::select('model_year', $modelYears, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-input-custom required name="color" label="Color" placeholder="" value="{{ old('color') }}"
                        class="col-md-2" />

                    <div class="col-md-2">
                        <x-label value="Racda" />
                        {{ Form::select('racda', ['Si', 'No', 'N/A'], null, ['class' => 'form-control']) }}
                    </div>

                    <x-input-custom name="serial_niv" label="Serial N.I.V" placeholder="" value="{{ old('serial_niv') }}"
                        class="col-md-4" />

                    <x-input-custom name="body_serial_number" label="Serial carrocería" placeholder=""
                        value="{{ old('body_serial_number') }}" class="col-md-4" />

                    <x-input-custom name="chassis_serial_number" label="Serial chasis" placeholder=""
                        value="{{ old('chassis_serial_number') }}" class="col-md-4" />

                    <x-input-custom name="engine_serial_number" label="Serial motor" placeholder=""
                        value="{{ old('engine_serial_number') }}" class="col-md-4" />

                </div>

                <hr>

                <h5 class="mb-3">Datos legales</h5>

                <div class="row">

                    <x-input-custom name="owner" label="Propietario" placeholder="" class="col-md-4"
                        value="{{ old('owner') }}" />

                    <x-input-custom name="origin" label="Origen" placeholder="" class="col-md-4"
                        value="{{ old('origin') }}" />

                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.equipment.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>



@stop

@section('js')
    <script>
        $(document).ready(function() {

        });
    </script>
@stop
