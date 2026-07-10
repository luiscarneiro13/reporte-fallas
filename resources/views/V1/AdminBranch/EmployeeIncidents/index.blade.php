@extends('adminlte::page')

@section('title', 'Incidencias de empleados')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')

    @php
        $headers = [
            ['label' => 'Empleado', 'field' => null],
            ['label' => 'Fecha', 'field' => 'date'],
            ['label' => 'Descripción', 'field' => null],
            ['label' => 'Reportado por', 'field' => null],
            ['label' => '', 'field' => null],
        ];
    @endphp

    <x-base-data-table-search title="Incidencias de empleados" :items="$incidents" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.employee.incidents.create') }}"
        :sortBy="$sortBy ?? null" :sortDir="$sortDir ?? 'desc'">
        <x-slot name="body">
            @forelse ($incidents as $item)
                <tr>
                    <td>{{ $item->employee?->last_name . ' ' . $item->employee?->first_name }}</td>
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


@stop
@section('customjs')

@stop
