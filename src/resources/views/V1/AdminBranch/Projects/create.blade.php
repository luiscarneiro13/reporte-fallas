@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')

    <h1>Crear proyecto</h1>

    @vite('resources/js/addCustomer.js')
    @vite('resources/js/addDivision.js')
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
                        <x-label value="División" btnAddModalTarget="#modalAddDivision" />
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

    <div id="addCustomer"></div>
    <div id="addDivision"></div>

@stop

---

@section('js')
    <script>
        $(document).ready(function() {

            window.branchId = {{ session('branch')->id ?? 'null' }};

            $('.select2').select2();

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
