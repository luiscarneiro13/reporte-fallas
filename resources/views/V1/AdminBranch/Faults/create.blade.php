@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Fallas')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">
        <h1>Reportar falla</h1>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.sucursal.faults.store') }}" method="POST">
        @csrf
        @if ($back_url)
            <input type="hidden" name="back_url" value="{{ $back_url }}">
        @endif
        <div class="card">
            <div class="card-body">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">Falla detectada</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">

                            <x-select label="Reportado por" name="employee_reported_id" :items="$employeeReported"
                                class="col-md-4" />

                            <x-select label="Equipo" name="equipment_id" :items="$equipment" class="col-md-4"
                                classControl="select2 form-control" />

                            <x-select label="Area de servicio" name="service_area_id" :items="$serviceArea" class="col-md-4"
                                classControl="select2 form-control" />

                            <x-adminlte-textarea name="description" label="Descripción de la falla" placeholder=""
                                fgroup-class="col-md-12 mt-3" value="{{ old('description') }}" />

                        </div>
                    </div>
                </div>

                <div class="row mt-5">

                    <a href="{{ request()->back_url ?? route('admin.sucursal.faults.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>

                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>

            </div>
        </div>
    </form>




@stop

@section('js')
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%'
            });

        });
    </script>
@stop
