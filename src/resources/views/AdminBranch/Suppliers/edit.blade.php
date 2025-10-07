@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar Proveedor</h1>
    <small>{{ $supplier->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="back_url" value="{{ request()->back_url }}">
                <input type="hidden" name="id" value="{{ $supplier->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del proveedor"
                        fgroup-class="col-md-4" value="{{ $supplier->name }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Teléfono del proveedor"
                        fgroup-class="col-md-4" value="{{ $supplier->phone }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="Email del proveedor"
                        fgroup-class="col-md-4" value="{{ $supplier->email }}" />
                    <x-adminlte-input name="address" label="Dirección" placeholder="Nombre del proveedor"
                        fgroup-class="col-md-12" value="{{ $supplier->address }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.suppliers.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
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
