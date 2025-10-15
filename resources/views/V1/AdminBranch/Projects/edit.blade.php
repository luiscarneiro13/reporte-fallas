@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Editar Proyecto</h1>
    <small>{{ $project->name }}</small>

    @vite('resources/js/addCustomer.js')
    @vite('resources/js/addDivision.js')
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $project->id }}">

                <div class="row">

                    <div class="col-md-5">
                        <x-label value="Cliente" btnAddModalTarget="#modalAddCustomer" />
                        {{ Form::select('customer_id', $customers, $project->customer_id, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-input-custom name="name" label="Nombre del proyecto" placeholder="" class="col-md-7"
                        value="{{ $project->name }}" />

                    <div class="col-md-5">
                        <x-label value="División" btnAddModalTarget="#modalAddDivision" />
                        {{ Form::select('division_id', $divisions, $project->division_id, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-input-custom name="contract_number" label="Nro. de Contrato (opcional)" placeholder=""
                        class="col-md-7" value="{{ $project->contract_number }}" />

                    <x-input-custom name="geographic_area" label="Area geográfica" placeholder="" class="col-md-12"
                        value="{{ $project->geographic_area }}" />

                    <x-adminlte-textarea name="description" label="Descripción (opcional)" placeholder=""
                        fgroup-class="col-md-12 mt-3">
                        {{ $project->description }}
                    </x-adminlte-textarea>

                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.projects.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                        icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

    <div id="addCustomer"></div>
    <div id="addDivision"></div>

@stop


@section('js')
    <script>
        $(document).ready(function() {

            window.branchId = {{ session('branch')->id ?? 'null' }};

            $('.select2').select2({
                width: '100%'
            });

            $("#modalAddCustomer").on('customerAdded', function(event, newCustomer) {
                $(this).modal("hide");
                var customerSelect = $('select[name="customer_id"]');
                var newOption = new Option(newCustomer.name, newCustomer.id, true, true);
                customerSelect.append(newOption);
                customerSelect.val(newCustomer.id).trigger('change');
            });

            $("#modalAddDivision").on('divisionAdded', function(event, newDivision) {
                $(this).modal("hide");
                var divisionSelect = $('select[name="division_id"]');
                var newOption = new Option(newDivision.name, newDivision.id, true, true);
                divisionSelect.append(newOption);
                divisionSelect.val(newDivision.id).trigger('change');
            });
        });
    </script>
@stop
