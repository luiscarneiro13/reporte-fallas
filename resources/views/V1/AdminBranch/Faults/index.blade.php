@extends('adminlte::page')

@section('title', 'Resumen de fallas')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')
    @php
        $headers = ['ID', 'Descripción', ''];
    @endphp

    <x-base-data-table-search title="Resumen de fallas" :items="$faults" :headers="$headers" >
        <x-slot name="body">
            @forelse ($faults as $item)
                <tr>
                    <td>{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }} </td>
                    <td>{{ $item->description }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    {{-- <a class="dropdown-item" href="{{ route('admin.sucursal.faults.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar" action="{{ route('admin.sucursal.faults.destroy', $item) }}"
                                        method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="dropdown-item" type="submit">
                                            <i class="fa fa-trash">&nbsp;</i>
                                            Eliminar
                                        </button>
                                    </form> --}}

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

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
