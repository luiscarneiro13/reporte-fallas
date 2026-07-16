<div class="card">
    <div class="card-body">
        {{-- Eliminé el div vacío innecesario --}}
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                {{-- Columna para el título y botón de agregar --}}
                <div class="col-sm-12 col-md-6">
                    @if (isset($title))
                        <label for="">
                            <div class="form-inline justify-content-between align-items-center">
                                <h4 class="mr-3">{{ $title }}</h4>
                                @if (isset($urlBtnAdd))
                                    {{-- @if (isset($permissions)) --}}
                                    {{-- @can($permissions) --}}
                                    <a href="{{ $urlBtnAdd }}" style="margin-top:-6px">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                    {{-- @endcan --}}
                                    {{-- @endif --}}
                                @endif

                            </div>
                        </label>
                    @endif
                </div>

                {{-- Columna para la búsqueda y el filtro --}}
                <div class="col-sm-12 col-md-6">
                    <div id="table2_filter" class="dataTables_filter">
                        <label>
                            {{-- d-flex fuerza que estén en la misma línea (searchInput y searchButton) --}}
                            <div class="d-flex">
                                <x-adminlte-input name="searchInput" id="searchInput"
                                    style="margin-right: 5px; flex-grow: 1;" />
                                <input type="submit" id="searchButton" class="form-control form-control-sm btn-primary"
                                    value="Filtrar">
                                <div style="flex: 1;" class="ml-2">
                                </div>
                            </div>
                        </label>
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

            {{-- C A M B I O C L A V E: table-responsive ahora envuelve a la tabla --}}
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

            {{-- Paginación y Contador --}}
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
            // ... (Tu código JavaScript para el filtro se mantiene igual)
            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            var queryValue = getQueryParam('query');
            if (queryValue) {
                $('#searchInput').val(queryValue);
            } else {
                $('#searchInput').val('');
            }

            document.getElementById('searchButton').addEventListener('click', function() {
                var query = document.getElementById('searchInput').value;
                window.location.href = window.location.pathname + '?query=' + encodeURIComponent(query);
            });

            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event
                        .preventDefault();
                    document.getElementById('searchButton').click();
                }
            });

            @if (isset($urlExcel))
                document.getElementById('excelButton').addEventListener('click', function() {
                    var params = new URLSearchParams();

                    var query = document.getElementById('searchInput').value.trim();
                    if (query !== '') {
                        params.append('query', query);
                    }

                    var sortBy = getQueryParam('sort_by');
                    var sortDir = getQueryParam('sort_dir');
                    if (sortBy) {
                        params.append('sort_by', sortBy);
                    }
                    if (sortDir) {
                        params.append('sort_dir', sortDir);
                    }

                    var excelUrl = "{{ $urlExcel }}";
                    var queryString = params.toString();
                    window.location.href = queryString ? (excelUrl + '?' + queryString) : excelUrl;
                });
            @endif
        });
    </script>
@stop

@section('customcss')
@stop
