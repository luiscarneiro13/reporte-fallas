@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Customers</h1>
@stop

@section('content')

    <div class="card">
        {{-- @can('Crear Cliente') --}}
        <div class="card-header">
            <a href="{{ route('customer.create') }}" class="btn btn-sm btn-primary float-right">Nuevo</a>
        </div>
        {{-- @endcan --}}

        <div class="card-body">
            @php
                $heads = [
                    'DNI',
                    'Nombre',
                    'Apellido',
                    'Email',
                    'Teléfono',
                    ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
                ];
                $config = [
                    'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                    'order' => [],
                ];
            @endphp

            <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config" striped hoverable
                bordered>
                @foreach ($customers as $item)
                    <tr>
                        <td>{{ $item->dni }}</td>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ $item->apellido }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->telefono }}</td>
                        <td>
                            <div class="input-group" style="cursor:pointer;">
                                <div>
                                    <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item" href="{{ route('customer.edit', $item) }}">
                                            <i class="fa fa-edit">&nbsp;</i>
                                            Editar
                                        </a>

                                        <a class="dropdown-item" href="{{ route('customer.show', $item) }}">
                                            <i class="fa fa-eye">&nbsp;</i>
                                            Ver datos
                                        </a>

                                        <div class="dropdown-divider"></div>
                                        @can('Eliminar Cliente')
                                            <form class="formEliminar" data-nombre="{{ $item->email }}"
                                                action="{{ route('customer.destroy', $item) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="dropdown-item" type="submit">
                                                    <i class="fa fa-trash">&nbsp;</i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>

@stop
