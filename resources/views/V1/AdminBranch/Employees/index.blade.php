@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = [
            ['label' => 'Cédula', 'field' => 'identification_number'],
            ['label' => 'Nombre', 'field' => null],
            ['label' => 'Teléfono', 'field' => 'phone_number'],
            ['label' => 'Cargo', 'field' => 'cargo_id'],
            ['label' => 'Dirección', 'field' => 'address'],
            ['label' => 'Usuario de sistema', 'field' => null],
            ['label' => 'Rol de sistema', 'field' => null],
            ['label' => '', 'field' => null],
        ];
    @endphp

    <x-employees-resume title="Empleados" :items="$employees" :headers="$headers"
        :urlBtnAdd="route('admin.sucursal.employees.create')" titlePrint="Listado de empleados"
        :urlPrint="route('employees.impAll')" :urlExcel="route('employees.excel')" titleExcel="Exportar empleados a Excel"
        :cargos="$cargos" :employeesForSelect="$employeesForSelect" :projects="$projects"
        :sortBy="$sortBy ?? null" :sortDir="$sortDir ?? 'asc'">
        <x-slot name="body">
            @forelse ($employees as $item)
                <tr>
                    <td>{{ $item->identification_number }}</td>
                    <td>{{ $item->last_name . ' ' . $item->first_name }}</td>
                    <td>{{ $item->phone_number }}</td>
                    <td>{{ $item->cargo->name ?? '' }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->users->first()?->email ?? '' }}</td>
                    <td>{{ $item->users->first()?->roles->first()?->name ?? '' }}</td>

                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.employees.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    {{-- <a class="dropdown-item" href="{{ route('admin.sucursal.employees.incidents', $item) }}">
                                        <i class="fa fa-exclamation-triangle">&nbsp;</i>
                                        Incidencias
                                    </a> --}}

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.employees.destroy', $item) }}" method="post">
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
    </x-employees-resume>

@stop

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
