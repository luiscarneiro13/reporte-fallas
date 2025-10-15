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
                                    <a href="{{ $urlBtnAdd }}" style="margin-top:-6px">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
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
                                {{-- Quitamos style="width: 300px;" para que sea responsivo --}}
                                <x-adminlte-input name="searchInput" id="searchInput"
                                    style="margin-right: 5px; flex-grow: 1;" />
                                <input type="submit" id="searchButton" class="form-control form-control-sm btn-primary"
                                    value="Filtrar">
                            </div>
                        </label>
                    </div>
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
        });
    </script>
@stop

@section('customcss')
@stop
