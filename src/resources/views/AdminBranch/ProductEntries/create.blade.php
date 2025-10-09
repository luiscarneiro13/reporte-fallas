@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Nueva entrada de producto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.product.entries.store') }}" method="POST">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <label for="product_id">Producto</label>
                        <a href="{{ route('admin.sucursal.products.create', ['back_url' => request()->url()]) }}"
                            class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                        {{ Form::select('product_id', $products, null, ['placeholder' => 'Seleccione', 'class' => 'select2 form-control']) }}
                    </div>

                    <div class="col-md-6">
                        <label for="supplier_id">Proveedor</label>
                        <a href="{{ route('admin.sucursal.suppliers.create', ['back_url' => request()->url()]) }}"
                            class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                        {{ Form::select('supplier_id', $suppliers, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="entry_qty" label="Cant. a ingresar" placeholder="Ej: 10"
                        fgroup-class="col-md-2 mt-3" value="{{ old('entry_qty') }}" />

                    <x-adminlte-input name="purchase_price" label="Precio compra $" placeholder="Ej: 10"
                        fgroup-class="col-md-2 mt-3" value="{{ old('purchase_price') }}" />

                    <x-adminlte-input name="selling_price" label="Precio venta $" placeholder="Ej: 10"
                        fgroup-class="col-md-2 mt-3" value="{{ old('selling_price') }}" />

                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.product.entries.index') }}"
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
            $('.select2').select2({
                width: '100%',
            });
        });
    </script>
@stop
