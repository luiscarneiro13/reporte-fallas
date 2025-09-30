@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Nuevo artículo</h1>
    @vite('resources/js/addBrand.js')
    @vite('resources/js/addModelVehicle.js')
    @vite('resources/js/addTypeArticle.js')
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.products.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <input type="hidden" name="back_url" value="{{ request()->back_url }}">
                            <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del producto"
                                fgroup-class="col-md-12" value="{{ old('name') }}" />

                            <div class="col-md-6">
                                <label for="type_article_id">Descripción</label>
                                <a href="#" data-toggle="modal" data-target="#modalAddTypeArticle"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('type_article_id', $typeArticles, null, ['class' => 'select2 form-control', 'id' => 'type_article_id']) }}
                            </div>

                            <div class="col-md-6">
                                <label for="brand_id">Marca del artículo</label>
                                <a href="#" data-toggle="modal" data-target="#modalAddBrand"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('brand_id', $brands, null, ['class' => 'select2 form-control', 'id' => 'brand_id']) }}
                            </div>

                            {{-- <div class="col-md-6">
                                <x-adminlte-input name="price" label="Precio en $" placeholder="Ej: 20.99"
                                    value="{{ old('available_qty') }}">
                                    <x-slot name="bottomSlot">
                                        <span class="text-gray text-sm">Ej: 100.99</span>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <x-adminlte-input name="available_qty" label="Cant. Disponible" placeholder="Ej: 10"
                                fgroup-class="col-md-6" value="{{ old('available_qty') }}">
                                <x-slot name="bottomSlot">
                                    <span class="text-gray text-sm">Ej: 10</span>
                                </x-slot>
                            </x-adminlte-input> --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="modelVehicles">Este artículo aplica a las siguientes marcas de vehículo</label>
                                <a href="#" data-toggle="modal" data-target="#modalAddModelVehicle"
                                    class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                                {{ Form::select('modelVehicles[]', $modelVehicles, null, ['class' => 'select2 form-control', 'id' => 'modelVehicles', 'multiple']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.products.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

    <div id="addBrand"></div>
    <div id="addModelVehicle"></div>
    <div id="addTypeArticle"></div>

@stop


@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
