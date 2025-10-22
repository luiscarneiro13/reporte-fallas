@extends('adminlte::page')

@section('title', 'Equipos')

@section('content_header')

@stop

@section('content')

    <div class="card">
        <div class="card-body">
            {{-- <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Equipo</h3>
                </div>
                <div class="card-body"> --}}
            <h3>
                <a href="{{ request()->back_url ?? route('admin.sucursal.equipment.index') }}" class="btn-sm mr-3 btn-default"
                    type="submit" icon="fas fa-lg fa-save">
                    << Volver</a>
                        {{ $equipment->fullEquipmentName }}

            </h3>
            {{-- HOLA
                </div>
            </div> --}}

            {{-- {{ json_encode($equipment->history) }} --}}

            @php
                $headers = ['ID', 'Falla', 'Solución', 'Completado', ''];
            @endphp

            {{-- Eliminé el div vacío innecesario --}}
            <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    {{-- Columna para el título y botón de agregar --}}
                    <div class="col-sm-12 col-md-6">
                        @if (isset($title))
                            <label for="">
                                <div class="form-inline justify-content-between align-items-center">
                                    <h4 class="mr-3">{{ $title }}</h4>
                                </div>
                            </label>
                        @endif
                    </div>

                    {{-- Columna para la búsqueda y el filtro --}}
                    <div class="col-sm-12 col-md-6">
                    </div>
                </div>

                {{-- C A M B I O C L A V E: table-responsive ahora envuelve a la tabla --}}
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="table2" style="width: 100%;"
                                class="table table-bordered table-hover table-striped dataTable no-footer">
                                <thead class="thead-dark">
                                    <tr role="row">
                                        @foreach ($headers as $item)
                                            <th>{{ $item }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($equipment->history as $item)
                                        <tr>
                                            <td>{{ str_pad($item->original_fault_id, 5, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->equipment_maintenance_log }}</td>
                                            <td>{{ $item->completed_execution }}</td>
                                            <td>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.sucursal.fault.history.show', [
                                                        'historico_falla' => $item,
                                                        'back_url' => request()->url(),
                                                    ]) }}">
                                                    <i class="fas fa-search">&nbsp;</i>
                                                    Ver detalles
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@stop
