@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Usuarios y Roles</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary float-right">Nuevo</a>
        </div>

        <div class="card-body">
            @php
                $heads = ['ID', 'Nombre', ['label' => 'Acciones', 'no-export' => true, 'width' => 5]];
                $config = [
                    'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                ];
            @endphp
            <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config" striped hoverable
                bordered>
                @foreach ($users as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <div class="input-group" style="cursor:pointer;">
                                <div>
                                    <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item" href="{{ route('assign_role.edit', $item) }}">
                                            <i class="fa fa-edit">&nbsp;</i>
                                            Editar Roles
                                        </a>

                                        <div class="dropdown-divider"></div>

                                        <form class="formEliminar" data-nombre="{{ $item->email }}"
                                            action="{{ route('assign_role.destroy', $item) }}" method="post">
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
