@extends('adminlte::page')

@section('title', 'Propietarios')

@section('content_header')
    {{-- <h1>Marcas de artículos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Apellido', 'Nombre', ''];
    @endphp

    <x-base-data-table-search title="Propietarios" :items="$owners" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.owners.create') }}">
        <x-slot name="body">
            @forelse ($owners as $item)
                <tr>
                    <td>{{ $item->last_name }}</td>
                    <td>{{ $item->first_name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.owners.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <form class="formEliminar" action="{{ route('admin.sucursal.owners.destroy', $item) }}"
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
