@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar entrada de producto </h1>
    <small>{{ $entry->product }}, Fecha: {{ $entry->updated_at }}, Cant. Ingresada:
        {{ $entry->entry_qty }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.product.entries.update', $entry) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $entry->id }}">
                <div class="row">

                    <div class="col-md-5">
                        <label for="product_id">Producto</label>
                        {{ Form::select('product_id', $products, $entry->product_id, ['placeholder' => 'Seleccione', 'class' => 'select2 form-control']) }}
                    </div>

                    <div class="col-md-5">
                        <label for="supplier_id">Proveedor</label>
                        {{ Form::select('supplier_id', $suppliers, $entry->supplier_id, ['placeholder' => 'Seleccione', 'class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="entry_qty" label="Cant. ingresada" placeholder="Ej: 10" fgroup-class="col-md-2"
                        value="{{ $entry->entry_qty }}" />

                    <x-adminlte-input name="purchase_price" label="Precio compra $" placeholder="Ej: 10"
                        fgroup-class="col-md-2 mt-3" value="{{ $entry->purchase_price }}" />

                    <x-adminlte-input name="selling_price" label="Precio venta $" placeholder="Ej: 10"
                        fgroup-class="col-md-2 mt-3" value="{{ $entry->selling_price }}" />

                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.product.entries.index') }}" class="btn-sm mr-3 btn-default"
                        type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
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
