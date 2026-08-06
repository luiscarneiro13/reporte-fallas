@extends('adminlte::page')

@section('title', 'Dashboard')

@section('adminlte_css')
    <style>
        .metric-grid {
            margin-bottom: 1.5rem;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            height: 100%;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: border-color .15s ease-in-out;
        }

        .metric-card__tint {
            position: absolute;
            inset: 0;
            opacity: .5;
            z-index: 0;
        }

        .metric-card__body {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .metric-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .metric-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .metric-card__badge {
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: .02em;
            border-radius: 9999px;
        }

        .metric-card__label {
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .metric-card__value {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.25;
            margin: 0;
        }

        .metric-card__accent {
            font-size: 18px;
        }

        /* Variantes de color */
        .metric-card--danger { border-color: #fca5a5; }
        .metric-card--danger:hover { border-color: #ef4444; }
        .metric-card--danger .metric-card__tint { background: #fef2f2; }
        .metric-card--danger .metric-card__icon { background: #fee2e2; color: #b91c1c; }
        .metric-card--danger .metric-card__badge { background: #fee2e2; color: #b91c1c; }
        .metric-card--danger .metric-card__label { color: #991b1b; }
        .metric-card--danger .metric-card__value { color: #7f1d1d; }
        .metric-card--danger .metric-card__accent { color: #dc2626; }

        .metric-card--success { border-color: #86efac; }
        .metric-card--success:hover { border-color: #22c55e; }
        .metric-card--success .metric-card__tint { background: #f0fdf4; }
        .metric-card--success .metric-card__icon { background: #dcfce7; color: #15803d; }
        .metric-card--success .metric-card__label { color: #166534; }
        .metric-card--success .metric-card__value { color: #14532d; }
        .metric-card--success .metric-card__accent { color: #16a34a; }

        .metric-card--warning { border-color: #fcd34d; }
        .metric-card--warning:hover { border-color: #eab308; }
        .metric-card--warning .metric-card__tint { background: #fffbeb; }
        .metric-card--warning .metric-card__icon { background: #fef3c7; color: #b45309; }
        .metric-card--warning .metric-card__badge { background: #fef3c7; color: #b45309; }
        .metric-card--warning .metric-card__label { color: #92400e; }
        .metric-card--warning .metric-card__value { color: #78350f; }

        .metric-card--neutral { border-color: #dee2e6; }
        .metric-card--neutral:hover { border-color: #adb5bd; }
        .metric-card--neutral .metric-card__icon { background: #dcfce7; color: #166534; }
        .metric-card--neutral .metric-card__label { color: #6c757d; }
        .metric-card--neutral .metric-card__value { color: #212529; }

        .metric-card--teal { border-color: #dee2e6; }
        .metric-card--teal:hover { border-color: #adb5bd; }
        .metric-card--teal .metric-card__icon { background: #ccfbf1; color: #0f766e; }
        .metric-card--teal .metric-card__label { color: #6c757d; }
        .metric-card--teal .metric-card__value { color: #212529; }

        .metric-card--blue { border-color: #dee2e6; }
        .metric-card--blue:hover { border-color: #adb5bd; }
        .metric-card--blue .metric-card__icon { background: #dbeafe; color: #1d4ed8; }
        .metric-card--blue .metric-card__label { color: #6c757d; }
        .metric-card--blue .metric-card__value { color: #212529; }
    </style>
@stop

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    {{-- CARD 1: Indicadores (Bento Grid) --}}
    <div class="row metric-grid">
        @if (isset($mostFailingEquipment['equipment_name']))
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="metric-card metric-card--danger">
                    <div class="metric-card__tint"></div>
                    <div class="metric-card__body">
                        <div class="metric-card__top">
                            <div class="metric-card__icon"><i class="fas fa-truck"></i></div>
                            <span class="metric-card__badge">Crítico</span>
                        </div>
                        <h3 class="metric-card__label">Equipo con más índice de fallas</h3>
                        <p class="metric-card__value">{{ $mostFailingEquipment['equipment_name'] }}
                            <span class="metric-card__accent">({{ $mostFailingEquipment['total_faults'] ?? 0 }})</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if (isset($mostFailReported['reported_by_name']))
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="metric-card metric-card--success">
                    <div class="metric-card__tint"></div>
                    <div class="metric-card__body">
                        <div class="metric-card__top">
                            <div class="metric-card__icon"><i class="fas fa-user"></i></div>
                        </div>
                        <h3 class="metric-card__label">Usuario con más fallas reportadas</h3>
                        <p class="metric-card__value">{{ $mostFailReported['reported_by_name'] }}
                            <span class="metric-card__accent">({{ $mostFailReported['total_reports'] ?? 0 }})</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="metric-card metric-card--warning">
                <div class="metric-card__tint"></div>
                <div class="metric-card__body">
                    <div class="metric-card__top">
                        <div class="metric-card__icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <span class="metric-card__badge">Activas</span>
                    </div>
                    <h3 class="metric-card__label">Fallas activas</h3>
                    <p class="metric-card__value">{{ $totalActiveFaults }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="metric-card metric-card--neutral">
                <div class="metric-card__body">
                    <div class="metric-card__top">
                        <div class="metric-card__icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <h3 class="metric-card__label">Fallas cerradas</h3>
                    <p class="metric-card__value">{{ $totalClosedFaults }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="metric-card metric-card--teal">
                <div class="metric-card__body">
                    <div class="metric-card__top">
                        <div class="metric-card__icon"><i class="fas fa-users"></i></div>
                    </div>
                    <h3 class="metric-card__label">Empleados activos</h3>
                    <p class="metric-card__value">{{ $totalActiveEmployees }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="metric-card metric-card--blue">
                <div class="metric-card__body">
                    <div class="metric-card__top">
                        <div class="metric-card__icon"><i class="fas fa-boxes"></i></div>
                    </div>
                    <h3 class="metric-card__label">Equipos activos</h3>
                    <p class="metric-card__value">{{ $totalActiveEquipment }}</p>
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
                    <x-chart minHeight="400px" title="Fallas por equipo (Últimos 10)" type="bar" :labels="$failuresByEquipment['labels']" :values="$failuresByEquipment['values']"
                        :show-percentages="true" />
                </div>
            </div>

            <div class="row">
                {{-- ... Otros gráficos sin cambios ... --}}
                <div class="col-md-6">
                    <x-chart title="Fallas por área de servicio" type="pie" :labels="$failuresByServiceArea['labels']" :values="$failuresByServiceArea['values']"
                        :show-percentages="true" />
                </div>
                 {{-- ... Otros gráficos sin cambios ... --}}
                <div class="col-md-6">
                    <x-chart title="Fallas por proyectos" type="pie" :labels="$failuresByProject['labels']" :values="$failuresByProject['values']"
                        :show-percentages="true" />
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-chart title="Fallas por usuario" type="pie" :labels="$failuresByReporter['labels']" :values="$failuresByReporter['values']"
                        :show-percentages="true" />
                </div>
                <div class="col-md-6">
                    <x-chart title="Fallas por estatus" type="pie" :labels="$failuresByStatus['labels']" :values="$failuresByStatus['values']"
                        :show-percentages="true" />
                </div>
            </div>
             {{-- ... Otros gráficos sin cambios ... --}}
            <div class="row">

                <div class="col-md-6">
                    <x-chart title="Fallas por status de repuestos" type="pie" :labels="$failuresBySparePartStatus['labels']" :values="$failuresBySparePartStatus['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-6">
                    <x-chart title="Fallas abiertas y cerradas" type="pie" :labels="$faultsByStatus['labels']" :values="$faultsByStatus['values']"
                        :show-percentages="true" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
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
