<div class="card">
    <div class="card-body">
        {{-- Eliminé el div vacío innecesario --}}
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row mb-2 align-items-end">
                <x-input-date-custom name="from" label="Desde" placeholder="" class="col-md-2" />
                <x-input-date-custom name="to" label="Hasta" placeholder="" class="col-md-2" />
                <x-input-custom name="searchInput" id="searchInput" class="col-md-6" label="Búsqueda" noMarginTop />

                <div class="col-md-2">
                    <input type="submit" id="searchButton" name="searchButton" class="btn btn-primary btn-block h-100"
                        value="Aplicar filtro">
                </div>
            </div>
        </div>
        {{-- <x-fault-filters /> --}}
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

            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            var queryValue = getQueryParam('query');
            $('#searchInput').val(queryValue || '');

            document.getElementById('searchButton').addEventListener('click', function() {
                const fromInput = document.querySelector('input[name="from"]');
                const toInput = document.querySelector('input[name="to"]');

                const fromValue = fromInput?.value.trim();
                const toValue = toInput?.value.trim();

                if (!isValidDateFormat(fromValue) || !isValidDateFormat(toValue)) {
                    alert('Las fechas deben tener el formato dd-mm-yyyy y contener solo números.');
                    return;
                }

                const fromDate = parseDate(fromValue);
                const toDate = parseDate(toValue);

                if (fromDate && toDate && fromDate > toDate) {
                    alert('La fecha "Desde" no puede ser mayor que "Hasta".');
                    return;
                }

                const query = document.getElementById('searchInput').value;
                window.location.href = window.location.pathname + '?query=' + encodeURIComponent(query);
            });

            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    document.getElementById('searchButton').click();
                }
            });

            function isValidDateFormat(str) {
                return /^\d{2}-\d{2}-\d{4}$/.test(str);
            }

            function parseDate(str) {
                const parts = str.split('-');
                if (parts.length !== 3) return null;
                const [day, month, year] = parts.map(Number);
                return new Date(year, month - 1, day);
            }
        });
    </script>
@stop

@section('customcss')
@stop
