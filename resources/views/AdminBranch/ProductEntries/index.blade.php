@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- <h1>Entrada de productos</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Producto', 'Proveedor', 'Cant. Ingresada', 'Fecha de ingreso', 'P. Compra $', 'P. Venta $', ''];
    @endphp

    <x-base-data-table-search title="Entrada de productos" :items="$entries" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.product.entries.create') }}">
        <x-slot name="body">
            @forelse ($entries as $item)
                <tr>
                    <td>{{ $item->product }}</td>
                    <td>{{ $item->supplier }}</td>
                    <td>{{ $item->entry_qty }}</td>
                    <td>{{ $item->date_ingress }}</td>
                    <td>{{ $item->purchase_price }}</td>
                    <td>{{ $item->selling_price }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.product.entries.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    {{-- <div class="dropdown-divider"></div> --}}
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.product.entries.destroy', $item) }}"
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

















    {{-- <div class="card"> --}}
    {{-- @can('Crear Cliente') --}}

    {{-- @endcan --}}

    {{-- <div class="card-body">
            @php
                $heads = [
                    'Producto',
                    'Proveedor',
                    'Cant. Ingresada',
                    'Fecha de ingreso',
                    'P. Compra',
                    'P. Venta',
                    ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
                ];
                $config = [
                    'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                    'order' => [],
                ];
            @endphp

            <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config" striped hoverable
                bordered>

            </x-adminlte-datatable>
        </div> --}}
    {{-- </div> --}}

@stop
@section('customjs')

@stop
