@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Sucursales</h1>
@stop

@section('content')

    <div class="card">
        {{-- @can('Crear Cliente') --}}
        <div class="card-header">
            <a href="{{ route('branches.create') }}" class="btn btn-sm btn-primary float-right">Nueva Sucursal</a>
        </div>
        {{-- @endcan --}}

        <div class="card-body">
            @php
                $heads = ['Sucursal', 'Teléfono', 'Email', 'Descripción', ['label' => 'Acciones', 'no-export' => true, 'width' => 5]];
                $config = [
                    'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                ];
            @endphp

            <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config" striped hoverable
                bordered>
                @foreach ($branches as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->description }}</td>
                        <td>
                            <div class="input-group" style="cursor:pointer;">
                                <div>
                                    <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item" href="{{ route('branches.edit', $item) }}">
                                            <i class="fa fa-edit">&nbsp;</i>
                                            Editar
                                        </a>

                                        <a class="dropdown-item" href="{{ route('branches.show', $item) }}">
                                            <i class="fa fa-eye">&nbsp;</i>
                                            Ver datos
                                        </a>

                                        <div class="dropdown-divider"></div>
                                        <form class="formEliminar" action="{{ route('branches.destroy', $item) }}"
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
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>

@stop
