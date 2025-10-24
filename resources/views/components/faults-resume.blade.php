<div class="card">
    <div class="card-body">
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row mb-2 align-items-end">

                <x-select label="Equipo" name="equipment_id" :items="$equipment" class="col-md-3"
                    classControl="select2 form-control" />

                <x-select label="Area de servicio" name="service_area_id" :items="$serviceArea" class="col-md-3"
                    classControl="select2 form-control" />

                <x-select label="Status de la falla" name="fault_status_id" :items="$faultStatus" class="col-md-3" />

                <x-select label="Status de repuestos" name="spare_part_status_id" :items="$sparePartStatuses" class="col-md-3"
                    classControl="select2 form-control" />

            </div>
            <div class="row mb-2 align-items-end">
                <x-select label="Proyectos" name="project_id" :items="$projects" class="col-md-3"
                    classControl="select2 form-control" />
                <x-input-date-custom name="from" label="Desde" placeholder="" class="col-md-2" />
                <x-input-date-custom name="to" label="Hasta" placeholder="" class="col-md-2" />
                <x-input-custom name="searchInput" id="searchInput" class="col-md-3" label="Búsqueda" noMarginTop />

                {{-- Contenedor para los botones --}}
                <div class="col-md-1 d-flex mt-3 mt-md-0">

                    @php
                        // Lógica de PHP para determinar si se aplicó cualquier filtro (selects, fechas o búsqueda)
                        $hasFilters =
                            request()->has('query') ||
                            request()->has('from') ||
                            request()->has('to') ||
                            (request()->has('equipment_id') && request('equipment_id') != '0') ||
                            (request()->has('service_area_id') && request('service_area_id') != '0') ||
                            (request()->has('fault_status_id') && request('fault_status_id') != '0') ||
                            (request()->has('spare_part_status_id') && request('spare_part_status_id') != '0') ||
                            (request()->has('project_id') && request('project_id') != '0');
                    @endphp

                    {{-- Botón Aplicar Filtro --}}
                    <div style="flex: 1;">
                        <button type="submit" id="searchButton" name="searchButton"
                            class="btn btn-primary btn-block h-100 mr-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    {{-- Botón Quitar Filtros --}}
                    <div style="flex: 1;" class="ml-2">
                        <button type="button" id="clearFiltersButton"
                            class="btn btn-secondary btn-block h-100 {{ $hasFilters ? '' : 'invisible' }}"
                            title="Quitar Filtros">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Botón Imprimir (MODIFICADO) --}}
                    <div style="flex: 1;" class="ml-2">
                        <button type="button" id="printButton" class="btn btn-default btn-block h-100"
                            title="Imprimir Reporte con Filtros">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                            {{ $body }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Paginación y Contador --}}
        <div class="row mt-2">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" id="table2_info" role="status" aria-live="polite">
                    Mostrando {{ $items->firstItem() }} a {{ $items->lastItem() }} de {{ $items->total() }}
                    registros
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers" id="table2_paginate">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@section('customjs')
    <script>
        // VARIABLE GLOBAL DE LA RUTA BASE DE IMPRESIÓN (establecida desde PHP)
        const BASE_PRINT_URL = "{{ route('faults.imp') }}";

        $(document).ready(function() {

            // ⭐ Aplica el foco al campo de búsqueda al cargar la página.
            $('#searchInput').focus();

            // --- Funciones Auxiliares ---

            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            // FUNCIÓN DE CONVERSIÓN 1: DD-MM-YYYY a YYYY-MM-DD (para la URL/MySQL)
            function parseDateForURL(str) {
                if (str === '') return '';
                const match = str.match(/^(\d{2})-(\d{2})-(\d{4})$/);
                if (match) {
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
                return str;
            }

            // FUNCIÓN DE CONVERSIÓN 2: YYYY-MM-DD a DD-MM-YYYY (para los filtros de input)
            function parseDateFromURL(str) {
                if (str === null || str === '') return '';
                const match = str.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (match) {
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
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
                const parts = str.split('-');
                if (parts.length !== 3) return null;
                const [day, month, year] = parts.map(Number);
                return new Date(year, month - 1, day);
            }

            // ⭐ Parámetros de los SELECTS: CLAVE para la inicialización
            const selectParams = ['equipment_id', 'service_area_id', 'fault_status_id', 'spare_part_status_id',
                'project_id'
            ];

            // ------------------------------------------
            // --- LÓGICA DE IMPRESIÓN (abrirEImprimir) ---
            // ------------------------------------------

            /**
             * Abre la URL de impresión en una nueva ventana, llama a la función print()
             * y la cierra automáticamente.
             * @param {string} printUrl - La URL completa (con parámetros de filtro) a imprimir.
             * @returns {void}
             */
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
                                // Antes de cerrar, verificamos si la ventana sigue abierta
                                if (printWindow && !printWindow.closed) {
                                    printWindow.close();
                                }
                            }, 500); // 500ms de espera

                        } catch (e) {
                            console.error('Error al intentar imprimir en la nueva ventana:', e);
                            // Si falla, se deja la ventana abierta para que el usuario pueda interactuar.
                        }
                    };
                } else {
                    // Usamos console.error en lugar de alert() como indica la restricción.
                    console.error('La ventana emergente fue bloqueada. Por favor, permítala para imprimir.');
                    // NOTA: En producción, usar un toast o modal para informar al usuario sin usar alert().
                }
            }


            // ------------------------------------------
            // --- FUNCIÓN REUTILIZABLE PARA OBTENER LA QUERY STRING CON FILTROS ---
            // ------------------------------------------

            /**
             * Recolecta todos los filtros del formulario, realiza validaciones y
             * devuelve la cadena de consulta (query string) para la URL.
             * @param {boolean} validateDates - Si se deben validar las fechas y mostrar errores.
             * @returns {string|null} La query string o null si la validación falla.
             */
            function getFilterQueryString(validateDates = true) {
                const fromInput = document.querySelector('input[name="from"]');
                const toInput = document.querySelector('input[name="to"]');

                const fromInputDate = (fromInput?.value.trim() || '');
                const toInputDate = (toInput?.value.trim() || '');
                const searchQuery = document.getElementById('searchInput').value.trim();

                // 1. Validación de Formato de Fechas
                if (!isValidDateFormat(fromInputDate) || !isValidDateFormat(toInputDate)) {
                    if (validateDates) {
                        console.error('Las fechas deben tener el formato dd-mm-yyyy y contener solo números.');
                    }
                    return null;
                }

                const fromDateObj = parseDate(fromInputDate);
                const toDateObj = parseDate(toInputDate);

                // 2. Validación de Rango de Fechas
                if (fromDateObj && toDateObj && fromDateObj > toDateObj) {
                    if (validateDates) {
                        console.error('La fecha "Desde" no puede ser mayor que "Hasta".');
                    }
                    return null;
                }

                // CONVERSIÓN: Pasar al formato MySQL (YYYY-MM-DD) para la URL
                const fromMySQL = parseDateForURL(fromInputDate);
                const toMySQL = parseDateForURL(toInputDate);

                // 3. Construcción de la URLSearchParams
                const newParams = new URLSearchParams();

                // A. Añadir parámetros de SELECTS
                selectParams.forEach(paramName => {
                    const selectedValue = $(`select[name="${paramName}"]`).val();
                    // Solo añade el parámetro si no es "Todos" (valor '0') y no es nulo/vacío
                    if (selectedValue !== '0' && selectedValue !== '' && selectedValue !== null) {
                        newParams.append(paramName, selectedValue);
                    }
                });

                // B. Añadir parámetros de FECHAS
                if (fromMySQL !== '') {
                    newParams.append('from', fromMySQL);
                }

                if (toMySQL !== '') {
                    newParams.append('to', toMySQL);
                }

                // C. Añadir parámetro de BÚSQUEDA
                if (searchQuery !== '') {
                    newParams.append('query', searchQuery);
                }

                return newParams.toString();
            }


            // ------------------------------------------
            // --- INICIALIZACIÓN DE VALORES DE FILTRO ---
            // ------------------------------------------

            // Inicializar inputs de búsqueda y fecha
            $('#searchInput').val(getQueryParam('query') || '');
            $('input[name="from"]').val(parseDateFromURL(getQueryParam('from')));
            $('input[name="to"]').val(parseDateFromURL(getQueryParam('to')));


            // ⭐ LÓGICA DE INICIALIZACIÓN DE SELECTS
            selectParams.forEach(paramName => {
                const paramValue = getQueryParam(paramName);
                const $select = $(`select[name="${paramName}"]`);

                if (paramValue !== null) {
                    $select.val(paramValue).trigger('change');
                } else {
                    $select.val('0').trigger('change');
                }
            });


            // ------------------------------------------
            // --- LÓGICA DEL BOTÓN DE BÚSQUEDA (Aplicar Filtro) ---
            // ------------------------------------------

            document.getElementById('searchButton').addEventListener('click', function() {
                // Reutilizamos la función para generar la query string
                const queryString = getFilterQueryString(true); // Pasamos true para validar y mostrar errores

                if (queryString === null) {
                    // Si getFilterQueryString devuelve null, significa que falló una validación
                    return;
                }

                let newUrl = window.location.pathname;
                if (queryString) {
                    newUrl += '?' + queryString;
                }

                window.location.href = newUrl;
            });


            // ------------------------------------------
            // --- LÓGICA DEL BOTÓN DE IMPRIMIR (NUEVO) ---
            // ------------------------------------------

            document.getElementById('printButton').addEventListener('click', function() {
                // 1. Obtiene la query string actual con los filtros (sin validación estricta, la validación se hace internamente)
                const queryString = getFilterQueryString(true);

                if (queryString === null) {
                    // Si getFilterQueryString devuelve null, significa que falló una validación
                    $('#searchInput').focus(); // Vuelve a enfocar para indicar un error
                    return;
                }

                // 2. Construye la URL de impresión completa
                let printUrl = BASE_PRINT_URL;
                if (queryString) {
                    printUrl += '?' + queryString;
                }

                // 3. Llama a la función de impresión
                abrirEImprimir(printUrl);
            });


            // Lógica del Botón "Quitar Filtro"
            document.getElementById('clearFiltersButton')?.addEventListener('click', function() {
                // Redirige a la URL base sin parámetros de consulta
                window.location.href = window.location.pathname;
            });

            // --- Evento de Teclado (Enter) ---
            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    document.getElementById('searchButton').click();
                }
            });
        });
    </script>
@stop

@section('customcss')
@stop
