@extends('adminlte::page')

@section('title', 'Ejecutores')

@section('content_header')
    <h1>Editar ejecutor</h1>
    <small>{{ $executor->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.executors.update', $executor) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $executor->id }}">

                <h5 class="mb-3">Datos básicos</h5>

                <div class="row">

                    {{-- Cédula, Nombre, Apellido: AHORA REQUERIDOS --}}
                    <x-input-custom name="identification_number" label="Cédula" placeholder="" class="col-md-4"
                        value="{{ $executor->identification_number }}" />

                    <x-input-custom name="first_name" label="Nombre" placeholder="" class="col-md-4"
                        value="{{ $executor->first_name }}" />

                    <x-input-custom name="last_name" label="Apellido" placeholder="" class="col-md-4"
                        value="{{ $executor->last_name }}" />

                    {{-- Teléfono y Dirección: NO REQUERIDOS --}}
                    <x-input-custom name="phone_number" label="Teléfono" placeholder="" class="col-md-4"
                        value="{{ $executor->phone_number }}" />

                    <div class="col-md-2">
                        <x-label value="Tipo" />
                        {{ Form::select('external', [0 => 'Interno', 1 => 'Externo'], $executor->external, ['class' => 'form-control']) }}
                    </div>

                    <x-input-custom name="address" label="Dirección (opcional)" placeholder="" class="col-md-6"
                        value="{{ $executor->address }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.executors.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>


@stop


@section('js')
    <script></script>
@stop
