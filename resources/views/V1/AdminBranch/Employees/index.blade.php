@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = ['Cédula', 'Nombre', 'Teléfono', 'Cargo', 'Dirección', 'Usuario de sistema', 'Rol de sistema', ''];
    @endphp

    <x-base-data-table-search title="Empleados" :items="$employees" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.employees.create') }}">
        <x-slot name="body">
            @forelse ($employees as $item)
                <tr>
                    <td>{{ $item->identification_number }}</td>
                    <td>{{ $item->lastname . ' ' . $item->first_name }}</td>
                    <td>{{ $item->phone_number }}</td>
                    <td>{{ $item->position }}</td>
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
    </x-base-data-table-search>

@stop

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
