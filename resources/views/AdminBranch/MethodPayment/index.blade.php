@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- <h1>Métodos de pago</h1> --}}
@stop

@section('content')

    @php
        $headers = ['Nombre', 'Moneda', ''];
    @endphp


    <x-base-data-table-search title="Métodos de pago" :items="$methodPayments" :headers="$headers"
        urlBtnAdd="{{ route('admin.sucursal.method.payment.create') }}">
        <x-slot name="body">
            @forelse ($methodPayments as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->currency }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.method.payment.edit', $item) }}">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    {{-- <a class="dropdown-item" href="{{ route('admin.sucursal.method.payment.show', $item) }}">
                            <i class="fa fa-eye">&nbsp;</i>
                            Ver datos
                        </a> --}}

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="{{ route('admin.sucursal.method.payment.destroy', $item) }}" method="post">
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
