@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Crear Cliente</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.customers.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif
                <div class="row">
                    <x-adminlte-input name="rif" label="RIF" placeholder="" fgroup-class="col-md-2"
                        value="{{ old('rif') }}" />

                    <x-adminlte-input name="name" label="Nombre" placeholder="" fgroup-class="col-md-5"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="" fgroup-class="col-md-3"
                        value="{{ old('email') }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="" fgroup-class="col-md-2"
                        value="{{ old('phone') }}" />
                    <x-adminlte-input name="address" label="Dirección" placeholder="" fgroup-class="col-md-12"
                        value="{{ old('address') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.customers.index') }}"
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
        console.log('Hi!');
    </script>
@stop
