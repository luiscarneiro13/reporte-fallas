<div class="card">
    <div class="card-body">
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <label>
                        <div class="form-inline justify-content-between align-items-center">
                            <h4 class="mr-3">{{ $title ?? 'Empleados' }}</h4>
                            @if (isset($urlBtnAdd))
                                <a href="{{ $urlBtnAdd }}" style="margin-top:-6px">
                                    <i class="fas fa-plus-circle"></i>
                                </a>
                            @endif
                        </div>
                    </label>
                </div>
            </div>

            <div class="row mb-2 align-items-end">
                <x-select label="Cargo" name="cargo_id" :items="$cargos" class="col-md-3"
                    classControl="select2 form-control" />

                <x-select label="Empleado" name="employee_id" :items="$employeesForSelect" class="col-md-3"
                    classControl="select2 form-control" />

                <x-select label="Proyecto" name="project_id" :items="$projects" class="col-md-3"
                    classControl="select2 form-control" />

                <x-select label="Area de servicio" name="service_area_id" :items="$serviceAreas" class="col-md-3"
                    classControl="select2 form-control" />
            </div>

            <div class="row mb-2 align-items-end">
                <x-input-custom name="identification_number" id="identificationNumberInput" class="col-md-3"
                    label="Cédula" noMarginTop />

                <x-input-custom name="name" id="nameInput" class="col-md-3" label="Nombre" noMarginTop />

                <div class="col-md-2 d-flex mt-3 mt-md-0">
                    @php
                        $hasFilters =
                            (request()->has('cargo_id') && request('cargo_id') != '0') ||
                            (request()->has('employee_id') && request('employee_id') != '0') ||
                            (request()->has('project_id') && request('project_id') != '0') ||
                            (request()->has('service_area_id') && request('service_area_id') != '0') ||
                            request()->filled('identification_number') ||
                            request()->filled('name');
                    @endphp

                    <div style="flex: 1;">
                        <button type="button" id="searchButton" class="btn btn-primary btn-block h-100 mr-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div style="flex: 1;" class="ml-2">
                        <button type="button" id="clearFiltersButton"
                            class="btn btn-secondary btn-block h-100 {{ $hasFilters ? '' : 'invisible' }}"
                            title="Quitar Filtros">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if (isset($titlePrint) || isset($urlExcel))
                <div class="row mb-2">
                    <div class="col-12 d-flex justify-content-start">
                        @if (isset($titlePrint))
                            <button type="button" id="printButton" class="btn btn-default" title="{{ $titlePrint }}">
                                <i class="fas fa-print"></i>
                            </button>
                        @endif

                        @if (isset($urlExcel))
                            <button type="button" id="excelButton" class="btn btn-success ml-2"
                                title="{{ $titleExcel ?? 'Exportar a Excel' }}">
                                <i class="fas fa-file-excel"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="table2" style="width: 100%;"
                            class="table table-bordered table-hover table-striped dataTable no-footer">
                            <thead class="thead-dark">
                                <tr role="row">
                                    @foreach ($headers as $item)
                                        @if (is_array($item))
                                            <x-sortable-th :label="$item['label']" :field="$item['field'] ?? null"
                                                :sortBy="$sortBy ?? null" :sortDir="$sortDir ?? 'asc'" />
                                        @else
                                            <th>{{ $item }}</th>
                                        @endif
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

            @if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->total() > 0)
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
            @endif
        </div>
    </div>
</div>

@section('customjs')
    <script>
        $(document).ready(function() {

            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            const selectParams = ['cargo_id', 'employee_id', 'project_id', 'service_area_id'];
            const textParams = ['identification_number', 'name'];

            function getFilterQueryString() {
                const newParams = new URLSearchParams();

                selectParams.forEach(function(paramName) {
                    const selectedValue = $(`select[name="${paramName}"]`).val();
                    if (selectedValue !== '0' && selectedValue !== '' && selectedValue !== null) {
                        newParams.append(paramName, selectedValue);
                    }
                });

                textParams.forEach(function(paramName) {
                    const input = document.querySelector(`input[name="${paramName}"]`);
                    const value = (input?.value || '').trim();
                    if (value !== '') {
                        newParams.append(paramName, value);
                    }
                });

                const sortBy = getQueryParam('sort_by');
                const sortDir = getQueryParam('sort_dir');
                if (sortBy) {
                    newParams.append('sort_by', sortBy);
                }
                if (sortDir) {
                    newParams.append('sort_dir', sortDir);
                }

                return newParams.toString();
            }

            // --- Inicialización de valores de filtro desde la URL ---
            selectParams.forEach(function(paramName) {
                const paramValue = getQueryParam(paramName);
                const $select = $(`select[name="${paramName}"]`);
                $select.val(paramValue !== null ? paramValue : '0').trigger('change');
            });

            textParams.forEach(function(paramName) {
                const input = document.querySelector(`input[name="${paramName}"]`);
                if (input) {
                    input.value = getQueryParam(paramName) || '';
                }
            });

            document.getElementById('searchButton').addEventListener('click', function() {
                const queryString = getFilterQueryString();
                window.location.href = window.location.pathname + (queryString ? '?' + queryString : '');
            });

            textParams.forEach(function(paramName) {
                document.querySelector(`input[name="${paramName}"]`)?.addEventListener('keypress',
                    function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            document.getElementById('searchButton').click();
                        }
                    });
            });

            document.getElementById('clearFiltersButton')?.addEventListener('click', function() {
                window.location.href = window.location.pathname;
            });

            @if (isset($titlePrint))
                document.getElementById('printButton').addEventListener('click', function() {
                    const queryString = getFilterQueryString();
                    let printUrl = "{{ $urlPrint ?? '' }}";
                    if (queryString) {
                        printUrl += '?' + queryString;
                    }

                    let printWindow = window.open(printUrl, '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            try {
                                printWindow.print();
                                setTimeout(function() {
                                    if (printWindow && !printWindow.closed) {
                                        printWindow.close();
                                    }
                                }, 500);
                            } catch (e) {
                                console.error('Error al intentar imprimir en la nueva ventana:', e);
                            }
                        };
                    } else {
                        console.error('La ventana emergente fue bloqueada. Por favor, permítala para imprimir.');
                    }
                });
            @endif

            @if (isset($urlExcel))
                document.getElementById('excelButton').addEventListener('click', function() {
                    const queryString = getFilterQueryString();
                    let excelUrl = "{{ $urlExcel }}";
                    window.location.href = queryString ? (excelUrl + '?' + queryString) : excelUrl;
                });
            @endif
        });
    </script>
@stop

@section('customcss')
@stop
