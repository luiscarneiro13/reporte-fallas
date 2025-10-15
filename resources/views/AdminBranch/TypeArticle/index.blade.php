@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- <h1>Descripción de artículos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Nombre', ''];
    @endphp

    <x-base-data-table-search title="Descripción de artículos" :items="$types" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.type.articles.create') }}">
        <x-slot name="body">
            @forelse ($types as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.type.articles.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.type.articles.destroy', $item) }}" method="post">
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
