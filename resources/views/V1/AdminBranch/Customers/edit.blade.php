@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Editar Cliente</h1>
    <small>{{ $customer->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $customer->id }}">
                <div class="row">
                    <x-adminlte-input name="rif" label="Rif" placeholder="" fgroup-class="col-md-2"
                        value="{{ $customer->rif }}" />

                    <x-adminlte-input name="name" label="Nombre" placeholder="" fgroup-class="col-md-5"
                        value="{{ $customer->name }}" />

                    <x-adminlte-input name="email" label="Email" placeholder="" fgroup-class="col-md-3"
                        value="{{ $customer->email }}" />

                    <x-adminlte-input name="phone" label="Teléfono" placeholder="" fgroup-class="col-md-2"
                        value="{{ $customer->phone }}" />

                    <x-adminlte-input name="address" label="Dirección" placeholder="Dirección" fgroup-class="col-md-12"
                        value="{{ $customer->address }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.customers.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                        icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
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
