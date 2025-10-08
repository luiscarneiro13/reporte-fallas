@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')

    <h1>Crear proyecto</h1>

    @vite('resources/js/addCustomer.js')
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.projects.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif
                <div class="row">

                    <div class="col-md-5">
                        <x-label value="Cliente" btnAddModalTarget="#modalAddCustomer" />
                        {{ Form::select('customer_id', $customers, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="name" label="Nombre del proyecto" placeholder="" fgroup-class="col-md-7"
                        value="{{ old('name') }}" />

                    <div class="col-md-5">
                        <x-label value="División"
                            btnAddUrl="{{ route('admin.sucursal.divisions.create', ['back_url' => request()->url()]) }}" />
                        {{ Form::select('division_id', $divisions, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="contract_number" label="Nro. de Contrato (opcional)" placeholder=""
                        fgroup-class="col-md-7" value="{{ old('contract_number') }}" />

                    <x-adminlte-input name="geographic_area" label="Area geográfica" placeholder="" fgroup-class="col-md-12"
                        value="{{ old('geographic_area') }}" />

                    <x-adminlte-textarea name="description" label="Descripción (opcional)" placeholder=""
                        fgroup-class="col-md-12" value="{{ old('description') }}" />

                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.projects.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

    {{-- El componente Vue renderizará el modal aquí --}}
    <div id="addCustomer"></div>

@stop


@section('js')
    <script>
        $(document).ready(function() {

            window.branchId = {{ session('branch')->id }};

            // Asegurar que Select2 esté inicializado
            $('.select2').select2();

            // 1. ESCUCHAR EL EVENTO PERSONALIZADO
            // Escuchamos el evento 'customerAdded' que se disparará desde el componente Vue.
            $("#modalAddCustomer").on('customerAdded', function(event, newCustomer) {

                // 2. CERRAR EL MODAL DE BOOTSTRAP
                // Usamos la función nativa de Bootstrap/jQuery para ocultar el modal.
                $(this).modal("hide");

                // 3. ACTUALIZAR EL SELECT2 (Campo Cliente)
                var customerSelect = $('select[name="customer_id"]');

                // Crear la nueva opción y seleccionarla
                var newOption = new Option(newCustomer.name, newCustomer.id, true, true);
                customerSelect.append(newOption);

                // Seleccionar y disparar el evento 'change' para que Select2 se actualice visualmente
                customerSelect.val(newCustomer.id).trigger('change');
            });
        });
    </script>
@stop
