@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Editar Sucursal {{ $branch->name }}</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-body">
            <form action="{{ route('admin.branch.my-branch.update', $branch) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <input type="hidden" name="id" value="{{ $branch->id }}">

                    <x-adminlte-input name="name" label="Nombre *" placeholder="Nombre de la Sucursal"
                        fgroup-class="col-md-8" value="{{ $branch->name }}" />

                    <x-adminlte-input name="rif" label="Rif *" placeholder="Rif de la Sucursal" fgroup-class="col-md-4"
                        value="{{ $branch->rif }}" />

                    <x-adminlte-input name="phone" label="Teléfono *" placeholder="Teléfono de la Sucursal"
                        fgroup-class="col-md-4" value="{{ $branch->phone }}" />

                    <x-adminlte-input name="email" label="Email *" placeholder="Email de la Sucursal"
                        fgroup-class="col-md-4" value="{{ $branch->email }}" />

                    <x-adminlte-textarea name="description" label="Descripción" placeholder="Descripción de la Sucursal"
                        fgroup-class="col-md-4">{{ $branch->description }}</x-adminlte-textarea>

                    <x-adminlte-input name="address" label="Dirección *" placeholder="Dirección de la Sucursal"
                        fgroup-class="col-md-12" value="{{ $branch->address }}" />

                    <div class="col-md-4">
                        <label>Suba el logo</label><br>
                        <input type="file" name="logo" id="logo" onchange="mostrar()">
                    </div>
                    <div class="col-md-4">
                        <img id="img" class="img-fluid"
                            src="{{ session('branch')->logo ? asset('storage/' . session('branch')->logo) : asset('logo.webp') }}" />
                    </div>

                </div>

                <div class="row mt-5">
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop


@section('js')
    <script>
        function mostrar() {
            var archivo = document.getElementById("logo").files[0];
            var reader = new FileReader();
            if (archivo) {
                reader.readAsDataURL(archivo);
                reader.onloadend = function() {
                    document.getElementById("img").src = reader.result;
                }
            }
        }
    </script>
@stop
