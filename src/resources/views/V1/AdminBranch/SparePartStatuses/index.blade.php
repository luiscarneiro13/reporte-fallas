@extends('adminlte::page')

@section('title', 'Status de repuestos')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Nombre',  ''];
    @endphp

    <x-base-data-table-search title="Status de repuestos" :items="$sparePartStatuses" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.spare.part.statuses.create') }}">
        <x-slot name="body">
            @forelse ($sparePartStatuses as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.spare.part.statuses.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.spare.part.statuses.destroy', $item) }}" method="post">
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
