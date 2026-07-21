@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Períodos de empleo')

@section('content_header')
    <h1>Nuevo período de empleo</h1>
    <small>{{ $employee->last_name }} {{ $employee->first_name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.employee.periods.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <x-input-date-custom required name="start_date" label="Fecha de ingreso" placeholder=""
                        class="col-md-3" value="{{ old('start_date') }}" />

                    <x-input-date-custom name="end_date" label="Fecha de egreso (dejar vacío si sigue activo)"
                        placeholder="" class="col-md-3" value="{{ old('end_date') }}" />

                    <div class="col-md-3">
                        <x-label value="Tipo de contrato" />
                        {{ Form::select('contract_type_id', $contractTypes, old('contract_type_id', '0'), ['class' => 'select2 form-control']) }}
                    </div>

                    <div class="col-md-3">
                        <x-label value="Cargo" />
                        {{ Form::select('cargo_id', $cargos, old('cargo_id', '0'), ['class' => 'select2 form-control']) }}
                    </div>
                </div>

                <div class="row">
                    <x-input-custom name="termination_reason" label="Motivo de egreso (opcional)" placeholder=""
                        class="col-md-6" value="{{ old('termination_reason') }}" />
                </div>

                <div class="row">
                    <x-textarea-custom name="notes" label="Notas (opcional)" placeholder="" class="col-md-12"
                        value="{{ old('notes') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ $back_url ?? route('admin.sucursal.employees.edit', $employee) }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop
