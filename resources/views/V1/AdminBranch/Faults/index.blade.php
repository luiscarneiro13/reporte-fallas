@extends('adminlte::page')

@section('title', 'Resumen de fallas')

@section('content_header')
    {{-- <h1>Proyectos</h1> --}}
@stop

@section('content')

    @php
        $headers = [
            'ID',
            'Código interno',
            'Equipo',
            'Descripción',
            'Status de falla',
            'Status de repuesto',
            'Area de servicio',
            'Tiempo en espera',
            '',
        ];
    @endphp

    <x-faults-resume title="Resumen de fallas" :items="$faults" :headers="$headers">
        <x-slot name="body">
            @forelse ($faults as $item)
                <tr>
                    <td>{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }} </td>
                    <td>{{ $item->equipment->internal_code }}</td>
                    <td>{{ $item->equipment->fullEquipmentName }}</td>
                    <td>{{ Str::limit($item->description, 20, '...') }}</td>
                    <td>{{ $item->faultStatus->name }}</td>
                    <td>{{ $item->sparePartStatus->name }}</td>
                    <td>{{ $item->serviceArea->name }}</td>
                    <td>
                        @if (!$item->closed)
                            {{ $item->days_since_report }}
                            <br>
                        @endif
                        <x-badge-button :name="$item->closed ? 'Cerrada' : 'Abierta'" :type="$item->closed ? 'success' : 'warning'" /> <br>
                    </td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div class="mt-3">
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    @if (!$item->closed)
                                        <a class="dropdown-item" href="{{ route('admin.sucursal.faults.edit', $item) }}">
                                            <i class="fa fa-edit">&nbsp;</i>
                                            Editar
                                        </a>

                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-check">&nbsp;</i>
                                            Cerrar falla
                                        </a>
                                    @else
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-check">&nbsp;</i>
                                            Ver datos
                                        </a>
                                    @endif
                                    {{--
                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar" action="{{ route('admin.sucursal.faults.destroy', $item) }}"
                                        method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="dropdown-item" type="submit">
                                            <i class="fa fa-trash">&nbsp;</i>
                                            Eliminar
                                        </button>
                                    </form>
--}}

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </x-slot>
    </x-faults-resume>

@stop

@section('js')
    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
