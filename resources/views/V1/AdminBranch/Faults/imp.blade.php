@extends('layouts.print-layout') {{-- 👈 Extiende la plantilla limpia de AdminLTE --}}

@section('content')

    <div class="card">
        <div class="card-body">

            {{-- HEADER: Logo | Títulos | Icono y Fecha --}}
            {{-- Usando id="impHeader" --}}
            <div id="impHeader" class="d-flex justify-content-between align-items-center mb-4">

                {{-- COLUMNA IZQUIERDA: LOGO --}}
                <div class="text-left col-4">
                    <img id="img" style="width: 100%; max-width: 200px; height: auto;"
                        src="{{ session('branch')->logo ? asset('storage/' . session('branch')->logo) : asset('logo.webp') }}"
                        alt="Logo de Sucursal" />
                </div>

                {{-- COLUMNA CENTRAL: TÍTULOS --}}
                <div class="text-center col-4">
                    <h3 style="margin-bottom: 0;">
                        Resumen de fallas
                    </h3>
                    <h5 style="margin-top: 0; font-weight: normal;">
                        {{-- {{ $equipment->fullEquipmentName }} --}}
                    </h5>
                </div>

                {{-- COLUMNA DERECHA: BOTÓN DE IMPRESIÓN Y FECHA DE EMISIÓN --}}
                <div class="text-right col-4">
                    {{-- Botón de Impresión --}}
                    {{-- <a href="#" class="btn-imprimir text-dark" onclick="imprimirTabla()">
                        <i class="fas fa-print fa-3x"></i>
                    </a> --}}

                    {{-- Fecha de Emisión (usando Carbon) --}}
                    @php
                        $fechaEmision = \Carbon\Carbon::now()->format('d-m-Y');
                    @endphp

                    {{-- La clase mt-2 añade un pequeño margen superior para separarlo del icono --}}
                    <div class="mt-2" style="font-size: 1rem; line-height: 1;">
                        <strong>Fecha:</strong> {{ $fechaEmision }}
                    </div>
                </div>
            </div>

            @php
                $headers = [
                    'ID',
                    'Código interno',
                    'Equipo',
                    'Descripción',
                    'Status de falla',
                    'Status de repuesto',
                    'Area de servicio',
                    'Tiempo en espera'
                ];
            @endphp

            {{-- CONTENIDO DE LA TABLA --}}
            <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        {{-- table-responsive ahora envuelve a la tabla --}}
                        <div class="table-responsive">
                            {{-- Usando id="tabla-a-imprimir" --}}
                            <table id="tabla-a-imprimir" style="width: 100%;"
                                class="table table-bordered table-hover table-striped dataTable no-footer">
                                <thead>
                                    <tr role="row">
                                        @foreach ($headers as $item)
                                            <th>{{ $item }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($faults as $item)
                                        <tr>
                                            <td>{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }} </td>
                                            <td>{{ $item->internal_code }}</td>
                                            <td>{{ $item->equipment_name }}</td>
                                            {{-- <td>{{ Str::limit($item->description, 20, '...') }}</td> --}}
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->fault_status_name }}</td>
                                            <td>{{ $item->spare_part_status_name }}</td>
                                            <td>{{ $item->service_area_name }}</td>
                                            <td>
                                                @if (!$item->duration_days)
                                                    Hoy
                                                    <br>
                                                @else
                                                    {{ $item->duration_days }} dias
                                                @endif
                                                {{-- <x-badge-button :name="$item->closed ? 'Cerrada' : 'Abierta'" :type="$item->closed ? 'success' : 'warning'" /> <br> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($headers) }}" class="text-center">No hay Resumen de
                                                fallas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> {{-- card-body --}}
    </div> {{-- card --}}

@endsection

@section('js')
    <script>
        // Función para el botón visible (si el usuario cierra el diálogo)
        // function imprimirTabla() {
        //     window.print();
        // }
    </script>
@stop
