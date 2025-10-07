<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        @if (isset($title))
                            <label for="">
                                <div class="form-inline">
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
                    <div class="col-sm-12 col-md-6">
                        <div id="table2_filter" class="dataTables_filter">
                            <label>
                                @php
                                    $config = [
                                        'showDropdowns' => true,
                                        'startDate' => 'js:moment()',
                                        'endDate' => 'js:moment()',
                                        'locale' => ['format' => 'YYYY/MM/DD'],
                                        'minYear' => 2024,
                                        'maxYear' => 2080,
                                        'timePicker' => false,
                                    ];
                                @endphp
                                <div class="form-inline">
                                    <x-adminlte-date-range name="searchInput" :config="$config" style="width: 300px;" />
                                    <input type="submit" id="searchButton"
                                        class="form-control form-control-sm btn-primary" value="Filtrar">
                                </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
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
</div>

@section('customjs')
    <script>
        $(document).ready(function() {
            // Función para obtener el valor de un parámetro de la URL
            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            // Obtener el valor del parámetro 'query'
            var queryValue = getQueryParam('query');
            if (queryValue) {
                $('#searchInput').val(queryValue);
            } else {
                $('#searchInput').val('');
            }

            //Botón de buscar
            document.getElementById('searchButton').addEventListener('click', function() {
                var query = document.getElementById('searchInput').value;
                window.location.href = window.location.pathname + '?query=' + encodeURIComponent(query);
            });

            // Disparar el botón de buscar al presionar Enter en el campo de búsqueda
            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event
                        .preventDefault(); // Previene el comportamiento predeterminado de enviar el formulario
                    document.getElementById('searchButton').click(); // Dispara el botón de buscar
                }
            });
        });
    </script>
@stop
@section('customcss')
@stop
