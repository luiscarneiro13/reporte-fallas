@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = ['ID', 'Código interno', 'Tipo', 'Placa', 'Marca', 'Modelo', 'Año', 'Color', 'Proyecto', ''];
    @endphp

    <x-base-data-table-search title="Equipos" :items="$equipment" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.equipment.create') }}">
        <x-slot name="body">
            @forelse ($equipment as $item)
                <tr>
                    <td>{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->internal_code }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->placa }}</td>
                    <td>{{ $item->brand_name }}</td>
                    <td>{{ $item->vehicle_model }}</td>
                    <td>{{ $item->model_year }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->lastProject?->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.equipment.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    @if (isset($item->history) && count($item->history) > 0)
                                        <a class="dropdown-item"
                                            href="{{ route('admin.sucursal.equipment.show', [
                                                'equipo' => $item,
                                                'back_url' => request()->url(),
                                            ]) }}">
                                            <i class="fas fa-history">&nbsp;</i>
                                            Histtórico de fallas
                                        </a>
                                    @endif

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.equipment.destroy', $item) }}" method="post">
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
