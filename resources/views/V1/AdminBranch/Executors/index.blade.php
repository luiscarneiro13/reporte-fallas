@extends('adminlte::page')

@section('title', 'Ejecutores')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = ['Tipo', 'Cédula', 'Nombre', 'Teléfono', 'Dirección', 'Aras de servicio', ''];
    @endphp

    <x-base-data-table-search title="Ejecutores" :items="$executors" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.executors.create') }}">
        <x-slot name="body">
            @forelse ($executors as $item)
                <tr>
                    <td>{{ $item->external ? 'Externo' : 'Interno' }}</td>
                    <td>{{ $item->identification_number }}</td>
                    <td>{{ $item->lastname . ' ' . $item->first_name }}</td>
                    <td>{{ $item->phone_number }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->executorServiceAreas?->pluck('name')?->implode(', ') ?? '' }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.executors.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.executors.destroy', $item) }}" method="post">
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
