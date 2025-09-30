@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar artículo</h1>
    <small>{{ $product->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="back_url" value="{{ request()->back_url }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del producto"
                                fgroup-class="col-md-12" value="{{ $product->name }}" />

                            <div class="col-md-6">
                                <label for="type_article_id">Descripción</label>
                                <a href="{{ url('admin-sucursal/tipos-articulos/create?back_url=' . url()->current()) }}"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('type_article_id', $typeArticles, $product->type_article_id, ['class' => 'select2 form-control']) }}
                            </div>

                            <div class="col-md-6">
                                <label for="brand_id">Marca</label>
                                <a href="{{ url('admin-sucursal/marcas/create?back_url=' . url()->current()) }}"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('brand_id', $brands, $product->brand_id, ['class' => 'select2 form-control']) }}
                            </div>

                            <x-adminlte-input name="price" label="Precio en $" placeholder="Ej: 20.99"
                                fgroup-class="col-md-6" value="{{ $product->available_qty }}">
                                <x-slot name="bottomSlot">
                                    <span class="text-gray text-sm">Ej: 100.99</span>
                                </x-slot>
                            </x-adminlte-input>

                            <x-adminlte-input name="available_qty" label="Cant. Disponible" placeholder="Ej: 100.99"
                                value="{{ $product->available_qty }}">
                                <x-slot name="bottomSlot">
                                    <span class="text-gray text-sm">Ej: 10</span>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                @php
                                    $modelVehiclesSelecteds = [];
                                    if (@$product->modelVehicles) {
                                        $modelVehiclesSelecteds = $product->modelVehicles->pluck('id');
                                    }
                                @endphp
                                <label for="modelVehicles">Este artículo aplica a las siguientes marcas de vehículo</label>
                                <a href="#" data-toggle="modal" data-target="#modalAddModelVehicle"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('modelVehicles[]', $modelVehicles, $modelVehiclesSelecteds, ['class' => 'select2 form-control', 'id' => 'modelVehicles', 'multiple']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.products.index') }}"
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
