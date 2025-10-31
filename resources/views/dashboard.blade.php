@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    {{-- CARD 1: Small Boxes (Sin Cambios) --}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                @if (isset($mostFailingEquipment['equipment_name']))
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <p>Equipo con más índice de fallas</p>
                                <h4>{{ $mostFailingEquipment['equipment_name'] }}
                                    ({{ $mostFailingEquipment['total_faults'] ?? 0 }})</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($mostFailReported['reported_by_name']))
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <p>Usuario con más fallas reportadas</p>
                                <h4>{{ $mostFailReported['reported_by_name'] }}
                                    ({{ $mostFailReported['total_reports'] ?? 0 }})</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Fallas activas</p>
                            <h4>{{ $totalActiveFaults }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <p>Fallas cerradas</p>
                            <h4>{{ $totalClosedFaults }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 2: Charts y Filtros --}}
    <div class="card">
        <div class="card-body">

            {{-- INICIO: Formulario de Filtro de Fechas --}}
            {{-- 🟢 Añadimos ID al formulario para manejar el submit con JS --}}
            <form method="GET" action="{{ route('dashboard') }}" id="dashboardFilterForm" class="mb-4">
                <div class="row align-items-end">

                    {{-- Nombre de campo: from_date --}}
                    {{-- 💡 Eliminamos 'value="{{ request('from_date') }}"' para dejar que el JS maneje la precarga --}}
                    <x-input-date-custom required name="from_date" label="Desde" placeholder="" class="col-md-3" />

                    {{-- Nombre de campo: to_date --}}
                    {{-- 💡 Eliminamos 'value="{{ request('to_date') }}"' para dejar que el JS maneje la precarga --}}
                    <x-input-date-custom required name="to_date" label="Hasta" placeholder="" class="col-md-3" />

                    {{-- Contenedor del Botón (con alineación mt-4) --}}
                    <div class="col-md-3 col-sm-6">
                        {{-- 🟢 Cambiamos type="submit" a type="button" y añadimos ID para la validación JS --}}
                        <button type="button" id="filterButton" class="btn btn-primary **mt-4**">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        {{-- <a href="{{ route('dashboard') }}" class="btn btn-secondary **mt-4 ml-2**">
                            <i class="fas fa-undo"></i> Limpiar
                        </a> --}}
                    </div>
                </div>
            </form>
            <hr>
            {{-- FIN: Formulario de Filtro de Fechas --}}

            {{-- ... Resto de tu código de gráficos ... --}}
            <div class="row">
                <div class="col-md-12">
                    <x-chart title="Fallas por equipo" type="bar" :labels="$failuresByEquipment['labels']" :values="$failuresByEquipment['values']"
                        :show-percentages="true" />
                </div>
            </div>

            <div class="row">
                {{-- ... Otros gráficos sin cambios ... --}}
                <div class="col-md-4">
                    <x-chart title="Fallas por área de servicio" type="pie" :labels="$failuresByServiceArea['labels']" :values="$failuresByServiceArea['values']"
                        :show-percentages="true" />
                </div>
                 {{-- ... Otros gráficos sin cambios ... --}}
                <div class="col-md-4">
                    <x-chart title="Fallas por proyectos" type="pie" :labels="$failuresByProject['labels']" :values="$failuresByProject['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas por usuario" type="pie" :labels="$failuresByReporter['labels']" :values="$failuresByReporter['values']"
                        :show-percentages="true" />
                </div>
            </div>
             {{-- ... Otros gráficos sin cambios ... --}}
            <div class="row">
                <div class="col-md-4">
                    <x-chart title="Fallas por estatus" type="pie" :labels="$failuresByStatus['labels']" :values="$failuresByStatus['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas por status de repuestos" type="pie" :labels="$failuresBySparePartStatus['labels']" :values="$failuresBySparePartStatus['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas abiertas y cerradas" type="pie" :labels="$faultsByStatus['labels']" :values="$faultsByStatus['values']"
                        :show-percentages="true" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <x-chart title="Fallas por división" type="pie" :labels="$failuresByDivision['labels']" :values="$failuresByDivision['values']"
                        :show-percentages="true" />
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    {{-- Agregamos InputMask si lo usa el componente (asumimos que sí) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>

    <script>
        $(document).ready(function() {
            // --- Funciones de Conversión y Validación ---

            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                // Usamos los nombres que tienes en tu formulario y controlador
                return urlParams.get(param);
            }

            // FUNCIÓN DE CONVERSIÓN: YYYY-MM-DD a DD-MM-YYYY (para los filtros de input)
            function parseDateFromURL(str) {
                if (str === null || str === '') return '';
                // Intentar parsear el formato YYYY-MM-DD (que es el formato de salida si se usa la lógica del otro Blade)
                const match = str.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (match) {
                    // Lo devolvemos como DD-MM-YYYY
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
                // Si ya está en DD-MM-YYYY (como en tu ejemplo de URL), lo dejamos.
                return str;
            }

            // Función para verificar el formato de fecha (DD-MM-YYYY)
            function isValidDateFormat(str) {
                if (str === '') return true;
                return /^\d{2}-\d{2}-\d{4}$/.test(str);
            }

            // Función para parsear la fecha a objeto Date para las comparaciones (from > to)
            function parseDate(str) {
                if (str === '') return null;
                // Espera DD-MM-YYYY
                const parts = str.split('-');
                if (parts.length !== 3) return null;
                const [day, month, year] = parts.map(Number);
                // Crea Date(YYYY, MM-1, DD)
                return new Date(year, month - 1, day);
            }

            // FUNCIÓN DE CONVERSIÓN: DD-MM-YYYY a YYYY-MM-DD (para la URL/MySQL)
            function parseDateForURL(str) {
                if (str === '') return '';
                const match = str.match(/^(\d{2})-(\d{2})-(\d{4})$/);
                if (match) {
                    // Devuelve YYYY-MM-DD
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
                return str;
            }

            /**
             * Recolecta los filtros de fecha, realiza validaciones y devuelve la query string.
             * @returns {string|null} La query string (ej: 'from_date=2025-01-01') o null si la validación falla.
             */
            function getFilterQueryString() {
                // Usamos los nombres de campo de tu formulario
                const fromInput = document.querySelector('input[name="from_date"]');
                const toInput = document.querySelector('input[name="to_date"]');

                const fromInputDate = (fromInput?.value.trim() || ''); // Formato DD-MM-YYYY
                const toInputDate = (toInput?.value.trim() || '');     // Formato DD-MM-YYYY

                // 1. Validación de Formato de Fechas
                if (!isValidDateFormat(fromInputDate) || !isValidDateFormat(toInputDate)) {
                    alert('El formato de fecha debe ser DD-MM-YYYY.'); // Puedes usar un alert más amigable
                    return null;
                }

                const fromDateObj = parseDate(fromInputDate);
                const toDateObj = parseDate(toInputDate);

                // 2. ⭐ VALIDACIÓN DE RANGO DE FECHAS (EL PUNTO CLAVE)
                if (fromDateObj && toDateObj && fromDateObj > toDateObj) {
                    alert('La fecha "Desde" no puede ser mayor que la fecha "Hasta".');
                    return null;
                }

                // CONVERSIÓN: Pasar al formato MySQL (YYYY-MM-DD) para la URL,
                // ya que tu controlador espera este formato para WHERE BETWEEN
                const fromMySQL = parseDateForURL(fromInputDate);
                const toMySQL = parseDateForURL(toInputDate);

                // 3. Construcción de la URLSearchParams (usando from_date y to_date)
                const newParams = new URLSearchParams();

                if (fromMySQL !== '') {
                    newParams.append('from_date', fromMySQL);
                }
                if (toMySQL !== '') {
                    newParams.append('to_date', toMySQL);
                }

                return newParams.toString();
            }

            // --- LÓGICA DE INICIALIZACIÓN (Persistencia de Fechas) ---

            // Lee la fecha de la URL (puede estar en DD-MM-YYYY o YYYY-MM-DD) y la convierte a DD-MM-YYYY para el input.
            const fromValue = parseDateFromURL(getQueryParam('from_date'));
            $('input[name="from_date"]').val(fromValue);

            const toValue = parseDateFromURL(getQueryParam('to_date'));
            $('input[name="to_date"]').val(toValue);


            // --- LÓGICA DEL BOTÓN DE FILTRAR ---

            // Asignar el evento al botón de filtro
            $('#filterButton').on('click', function() {
                const queryString = getFilterQueryString();

                if (queryString === null) {
                    // Si falla la validación, detener el proceso
                    return;
                }

                let newUrl = window.location.pathname;
                if (queryString) {
                    newUrl += '?' + queryString;
                }

                // Redirigir a la URL construida
                window.location.href = newUrl;
            });

            // Para que la tecla Enter también funcione
            $('input[name="from_date"], input[name="to_date"]').on('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    $('#filterButton').click();
                }
            });

        });
    </script>
@stop
