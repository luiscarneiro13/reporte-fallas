@extends('adminlte::page')

@section('title', 'Incidencias del empleado')

@section('content_header')
    <h1>Incidencias del empleado</h1>
@stop

@section('content')

    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">{{ $employee->last_name }} {{ $employee->first_name }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <x-label-value-horizontal class="col-md-4" label="Cédula" :value="$employee->identification_number" />
                <x-label-value-horizontal class="col-md-4" label="Teléfono" :value="$employee->phone_number" />
                <x-label-value-horizontal class="col-md-4" label="Cargo" :value="$employee->cargo?->name" />

                <x-label-value-horizontal class="col-md-4" label="Fecha de ingreso"
                    :value="$employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d-m-Y') : ''" />
                <x-label-value-horizontal class="col-md-4" label="Tipo de contrato"
                    :value="$employee->contractType?->name" />
                <x-label-value-horizontal class="col-md-4" label="Ejecutor de servicio"
                    :value="$employee->executor ? 'Sí' : 'No'" />

                <x-label-value-horizontal class="col-md-4" label="Proyecto(s)"
                    :value="$employee->projects->pluck('name')->join(', ')" />
                <x-label-value-horizontal class="col-md-4" label="Usuario de sistema"
                    :value="$employee->users->first()?->email" />
                <x-label-value-horizontal class="col-md-4" label="Rol de sistema"
                    :value="$employee->users->first()?->roles->first()?->name" />

                <x-label-value-horizontal class="col-md-12" label="Dirección" :value="$employee->address" />
            </div>
        </div>
    </div>

    @php
        $headers = [
            ['label' => 'Fecha', 'field' => null],
            ['label' => 'Descripción', 'field' => null],
            ['label' => 'Reportado por', 'field' => null],
            ['label' => '', 'field' => null],
        ];
    @endphp

    <x-base-data-table-search title="Incidencias" :items="$incidents" :headers="$headers"
        :urlBtnAdd="route('admin.sucursal.employee.incidents.create', ['employee_id' => $employee->id, 'back_url' => route('admin.sucursal.employees.incidents', $employee)])">
        <x-slot name="body">
            @forelse ($incidents as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->reportedBy?->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item"
                                        href="{{ route('admin.sucursal.employee.incidents.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.employee.incidents.destroy', $item) }}"
                                        method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="dropdown-item" type="submit">
                                            <i class="fa fa-trash">&nbsp;</i>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </x-slot>
    </x-base-data-table-search>

    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('admin.sucursal.employees.index') }}" class="btn-sm btn-default">Volver</a>
        </div>
    </div>

@stop
