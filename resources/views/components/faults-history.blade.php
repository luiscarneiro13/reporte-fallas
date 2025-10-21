<div class="card">
    <div class="card-body">
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row mb-2 align-items-end">
                <x-input-date-custom name="from" label="Desde" placeholder="" class="col-md-2" />
                <x-input-date-custom name="to" label="Hasta" placeholder="" class="col-md-2" />
                <x-input-custom name="searchInput" id="searchInput" class="col-md-4" label="Búsqueda" noMarginTop />

                {{-- Contenedor para los botones --}}
                <div class="col-md-3 d-flex">

                    @php
                        // Lógica de PHP para determinar si se aplicó cualquier filtro (selects, fechas o búsqueda)
                        $hasFilters =
                            request()->has('query') ||
                            request()->has('from') ||
                            request()->has('to');
                    @endphp

                    {{-- El botón Aplicar Filtro siempre usa mr-2 para dejar espacio a la derecha --}}
                    <input type="submit" id="searchButton" name="searchButton"
                        class="btn btn-primary btn-block h-100 mr-2" value="Aplicar filtro" style="flex: 1;">

                    {{-- Ajuste Clave: Contenedor con ancho fijo que siempre existe (visibility: hidden) --}}
                    <div style="flex: 1;">
                        <button type="button" id="clearFiltersButton"
                            class="btn btn-secondary btn-block h-100 {{ $hasFilters ? '' : 'invisible' }}">
                            Quitar filtro
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

---

@section('customjs')
    <script>
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

            // ------------------------------------------
            // --- INICIALIZACIÓN DE VALORES DE FILTRO ---
            // ------------------------------------------

            // Inicializar inputs de búsqueda y fecha
            $('#searchInput').val(getQueryParam('query') || '');
            $('input[name="from"]').val(parseDateFromURL(getQueryParam('from')));
            $('input[name="to"]').val(parseDateFromURL(getQueryParam('to')));



            // ------------------------------------------
            // --- LÓGICA DEL BOTÓN DE BÚSQUEDA (Aplicar Filtro) ---
            // ------------------------------------------

            document.getElementById('searchButton').addEventListener('click', function() {
                const fromInput = document.querySelector('input[name="from"]');
                const toInput = document.querySelector('input[name="to"]');

                // Valores en formato DD-MM-YYYY desde los inputs
                const fromInputDate = (fromInput?.value.trim() || '');
                const toInputDate = (toInput?.value.trim() || '');
                const searchQuery = document.getElementById('searchInput').value.trim();

                // 1. Validación de Formato de Fechas
                if (!isValidDateFormat(fromInputDate) || !isValidDateFormat(toInputDate)) {
                    alert('Las fechas deben tener el formato dd-mm-yyyy y contener solo números.');
                    $('#searchInput').focus();
                    return;
                }

                const fromDateObj = parseDate(fromInputDate);
                const toDateObj = parseDate(toInputDate);

                // 6. Validación de Rango de Fechas
                if (fromDateObj && toDateObj && fromDateObj > toDateObj) {
                    alert('La fecha "Desde" no puede ser mayor que "Hasta".');
                    $('#searchInput').focus();
                    return;
                }

                // CONVERSIÓN: Pasar al formato MySQL (YYYY-MM-DD) para la URL
                const fromMySQL = parseDateForURL(fromInputDate);
                const toMySQL = parseDateForURL(toInputDate);

                // 2. Construcción de la URL
                const newParams = new URLSearchParams();


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

                const queryString = newParams.toString();

                let newUrl = window.location.pathname;
                if (queryString) {
                    newUrl += '?' + queryString;
                }

                window.location.href = newUrl;
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
