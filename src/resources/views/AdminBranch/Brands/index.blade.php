@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- <h1>Marcas de artículos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Nombre', ''];
    @endphp

    <x-base-data-table-search title="Marcas de artículos" :items="$brands" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.brands.create') }}">
        <x-slot name="body">
            @forelse ($brands as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.brands.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    {{-- <a class="dropdown-item" href="{{ route('admin.sucursal.brands.show', $item) }}">
                                    <i class="fa fa-eye">&nbsp;</i>
                                    Ver datos
                                </a> --}}

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar" action="{{ route('admin.sucursal.brands.destroy', $item) }}"
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
