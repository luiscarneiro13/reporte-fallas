@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- <h1>Artículos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Descripción', 'Nombre', 'Cant. Disp.', 'Precio $', 'Marca', ''];
    @endphp

    <x-base-data-table-search title="Artículos / Productos" :items="$products" :headers="$headers">
        <x-slot name="body">
            @forelse ($products as $item)
                <tr>
                    <td>{{ $item->type_article }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->available_qty }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->brand }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.products.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.products.destroy', $item) }}" method="post">
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
