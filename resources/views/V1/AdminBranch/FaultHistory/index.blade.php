@extends('adminlte::page')
@section('title', 'Histórico de fallas')
@section('titleSup', 'Histórico de fallas')

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
            'Fecha de cierre',
            '',
        ];
    @endphp

    <x-faults-history title="Histórico de fallas" :items="$history" :headers="$headers">
        <x-slot name="body">
            @forelse ($history as $item)
                <tr>
                    <td>{{ str_pad($item->original_fault_id, 5, '0', STR_PAD_LEFT) }} </td>
                    <td>{{ $item->internal_code }}</td>
                    <td>{{ $item->equipment_name }}</td>
                    <td>{{ Str::limit($item->description, 20, '...') }}</td>
                    <td>{{ $item->fault_status_name }}</td>
                    <td>{{ $item->spare_part_status_name }}</td>
                    <td>{{ $item->service_area_name }}</td>
                    <td>{{ $item->closed_at->format('d-m-Y') }}</td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div class="mt-3">
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{ route('admin.sucursal.fault.history.show', $item) }}">
                                        <i class="fas fa-search">&nbsp;</i>
                                        Ver detalles
                                    </a>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </x-slot>
    </x-faults-history>

@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>

    <script>
        window.branchId = {{ session('branch')->id }};
    </script>
@stop
