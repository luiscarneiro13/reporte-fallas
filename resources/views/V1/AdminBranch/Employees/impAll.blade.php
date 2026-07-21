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
                        Empleados
                    </h3>
                    <h5 style="margin-top: 0; font-weight: normal;">
                    </h5>
                </div>

                {{-- COLUMNA DERECHA: BOTÓN DE IMPRESIÓN Y FECHA DE EMISIÓN --}}
                <div class="text-right col-4">
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
                $headers = ['Cédula', 'Nombre', 'Teléfono', 'Cargo', 'Dirección', 'Usuario de sistema', 'Rol de sistema'];
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
                                    @forelse ($employees as $item)
                                        <tr>
                                            <td>{{ $item->identification_number }}</td>
                                            <td>{{ $item->last_name . ' ' . $item->first_name }}</td>
                                            <td>{{ $item->phone_number }}</td>
                                            <td>{{ $item->cargo->name ?? '' }}</td>
                                            <td>{{ $item->address }}</td>
                                            <td>{{ $item->users->first()?->email ?? '' }}</td>
                                            <td>{{ $item->users->first()?->roles->first()?->name ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($headers) }}" class="text-center">No hay empleados.</td>
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
    </script>
@stop
