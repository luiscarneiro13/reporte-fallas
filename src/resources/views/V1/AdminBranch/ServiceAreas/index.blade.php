@extends('adminlte::page')

@section('title', 'Areas de servicio')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Nombre', 'Descripción', ''];
    @endphp

    <x-base-data-table-search title="Areas de servicio" :items="$serviceAreas" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.service.areas.create') }}">
        <x-slot name="body">
            @forelse ($serviceAreas as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->description }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.service.areas.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    {{-- <a class="dropdown-item" href="{{ route('admin.sucursal.brands.show', $item) }}">
                                    <i class="fa fa-eye">&nbsp;</i>
                                    Ver datos
                                </a> --}}

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.service.areas.destroy', $item) }}" method="post">
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
