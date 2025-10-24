@extends('adminlte::page')

@section('title', 'Equipos')

@section('content_header')

@stop

@section('content')

    <div class="card">
        <div class="card-body">

            <div class="form-inline justify-content-between align-items-center">
                <div>
                    <h3>
                        <a href="{{ request()->back_url ?? route('admin.sucursal.equipment.index') }}"
                            class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">
                            << Volver </a>
                                {{ $equipment->fullEquipmentName }}

                    </h3>
                </div>
                <h3>
                    @php
                        // Guardamos la URL de la vista de impresión en una variable Blade
                        $printUrl = route('equipos-fallas.imp', ['equipo' => $equipment->id]);
                    @endphp

                    <button type="button" class="btn btn-default" onclick="abrirEImprimir('{{ $printUrl }}')"
                        title="Imprimir Reporte">
                        <i class="fas fa-print"></i>
                    </button>
                </h3>
            </div>


            @php
                $headers = [
                    'ID',
                    'Fecha de falla',
                    'Falla',
                    'Fecha de corrección',
                    'Corrección',
                    'Ejecutor',
                    'Dias en espera',
                    '',
                ];
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
                                            <td>{{ str_pad($item->original_fault_id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $item->report_date->format('d-m-Y') }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->completed_execution->format('d-m-Y') }}</td>
                                            <td>{{ $item->equipment_maintenance_log }}</td>
                                            <td>{{ $item->executor_name }}</td>
                                            <td>
                                                @if ($item->completed_execution)
                                                    {{ $item->report_date->clone()->startOfDay()->diffInDays($item->completed_execution->clone()->startOfDay()) }}
                                                    días
                                                @else
                                                    En espera
                                                @endif
                                            </td>
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

@section('js')
    <script>
        function abrirEImprimir(printUrl) {
            // Abre la URL en una nueva ventana/pestaña
            let printWindow = window.open(printUrl, '_blank');

            // Asegura que la ventana se haya abierto correctamente
            if (printWindow) {
                // Espera a que el contenido de la nueva ventana termine de cargar
                printWindow.onload = function() {
                    try {
                        // 1. Llama a la función de impresión en la nueva ventana
                        printWindow.print();

                        // 2. Cierra la ventana después de un breve retraso
                        // (Necesario porque print() es asíncrono y no siempre se resuelve bien)
                        setTimeout(function() {
                            printWindow.close();
                        }, 500); // 500ms de espera

                    } catch (e) {
                        console.error('Error al imprimir en la nueva ventana:', e);
                        // Si falla (por bloqueo de pop-ups, etc.), deja la ventana abierta
                    }
                };
            } else {
                alert('La ventana emergente fue bloqueada. Por favor, permítala para imprimir.');
            }
        }
    </script>
@stop
