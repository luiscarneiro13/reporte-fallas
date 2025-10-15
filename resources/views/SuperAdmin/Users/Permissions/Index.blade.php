@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Permisos</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <x-adminlte-button class="btn btn-sm float-right" type="submit" label="Nuevo" theme="primary" data-toggle="modal"
                data-target="#modalPurple"/>
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
                @foreach ($permissions as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <div class="input-group" style="cursor:pointer;">
                                <div>
                                    <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                    <div class="dropdown-menu">

                                        <div class="dropdown-divider"></div>

                                        <form class="formEliminar" action="{{ route('permissions.destroy', $item) }}"
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

    <x-adminlte-modal id="modalPurple" title="Nuevo Permiso" theme="primary" size='lg'
        disable-animations>
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="nombre" label="Nombre" placeholder="Aquí su permiso" fgroup-class="col-md-6"
                    disable-feedback />
            </div>
            <div class="modal-footer">
                <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary" icon="fas fa-save" />
            </div>
            <x-slot name="footerSlot"></x-slot>
        </form>
    </x-adminlte-modal>

@stop
