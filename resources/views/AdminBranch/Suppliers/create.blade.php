@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Nuevo proveedor</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.suppliers.store') }}" method="POST">
                @csrf
                <input type="hidden" name="back_url" value="{{ request()->back_url }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del proveedor" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Teléfono del proveedor"
                        fgroup-class="col-md-4" value="{{ old('phone') }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="Email del proveedor"
                        fgroup-class="col-md-4" value="{{ old('email') }}" />
                    <x-adminlte-input name="address" label="Dirección" placeholder="Nombre del proveedor"
                        fgroup-class="col-md-12" value="{{ old('address') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.suppliers.index') }}"
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
