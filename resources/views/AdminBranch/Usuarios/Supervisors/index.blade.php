@extends('adminlte::page')

@section('title', 'Supervisores')

@section('content')

    @php
        $headers = ['Nombre', 'Email', 'Teléfono', ''];
    @endphp

    <x-base-data-table-search title="Supervisores" :items="$supervisors" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.usuarios.supervisors.create') }}">
        <x-slot name="body">
            @forelse ($supervisors as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item"
                                        href="{{ route('admin.sucursal.usuarios.supervisors.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.usuarios.supervisors.destroy', $item) }}"
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
