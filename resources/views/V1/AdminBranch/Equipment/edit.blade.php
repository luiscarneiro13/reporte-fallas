@extends('adminlte::page')

@section('title', 'Equipos')

@section('content_header')
    <h1>Editar Equipo</h1>
    {{-- <small>{{ $equipment->placa . ' - ' . $equipment->brand_name . ' - ' . $equipment->vehicle_model . ' - ' . $equipment->model_year . ' - ' . $equipment->color }}</small> --}}

@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.equipment.update', $equipment) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $equipment->id }}">

                <div class="row">
                    <x-input-custom name="internal_code" label="Código interno" placeholder="" class="col-md-3"
                        value="{{ $equipment->internal_code }}" />

                    <div class="col-md-5">
                        <x-label value="Proyecto" />
                        {{ Form::select('project_id', $projects, null, ['class' => 'select2 form-control']) }}
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Datos físicos</h5>

                <div class="row">

                    <x-input-custom required name="placa" placeholder="" value="{{ $equipment->placa }}" label="Placa"
                        class="col-md-2" />

                    <x-input-custom required name="brand_name" placeholder="" value="{{ $equipment->brand_name }}"
                        label="Marca" class="col-md-2" />

                    <x-input-custom required name="vehicle_model" label="Modelo" placeholder=""
                        value="{{ $equipment->vehicle_model }}" class="col-md-2" />

                    <div class="col-md-2">
                        <x-label value="Año" />
                        {{ Form::select('model_year', $modelYears, $equipment->model_year, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-input-custom required name="color" label="Color" placeholder="" value="{{ $equipment->color }}"
                        class="col-md-2" />

                    <div class="col-md-2">
                        <x-label value="Racda" />
                        {{ Form::select('racda', ['Si', 'No', 'N/A'], $equipment->racda, ['class' => 'form-control']) }}
                    </div>

                    <x-input-custom name="serial_niv" label="Serial N.I.V" placeholder=""
                        value="{{ $equipment->serial_niv }}" class="col-md-4" />

                    <x-input-custom name="body_serial_number" label="Serial carrocería" placeholder=""
                        value="{{ $equipment->body_serial_number }}" class="col-md-4" />

                    <x-input-custom name="chassis_serial_number" label="Serial motor" placeholder=""
                        value="{{ $equipment->chassis_serial_number }}" class="col-md-4" />

                </div>

                <hr>

                <h5 class="mb-3">Datos legales</h5>

                <div class="row">

                    <x-input-custom name="owner" label="Propietario" placeholder="" class="col-md-4"
                        value="{{ $equipment->owner }}" />

                    <x-input-custom name="origin" label="Origen" placeholder="" class="col-md-4"
                        value="{{ $equipment->origin }}" />

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
