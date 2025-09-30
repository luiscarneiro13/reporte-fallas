@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Historial de ventas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-6"> </div>
                        <div class="col-sm-12 col-md-6">
                            <div id="table2_filter" class="dataTables_filter">
                                <label>
                                    @php
                                        $config = [
                                            'showDropdowns' => true,
                                            'startDate' => 'js:moment()',
                                            'endDate' => 'js:moment()',
                                            'locale' => ['format' => 'YYYY-MM-DD'],
                                            'minYear' => 2024,
                                            'maxYear' => 2080,
                                            'timePicker' => false,
                                        ];
                                    @endphp
                                    <div class="form-inline">
                                        <x-adminlte-date-range name="searchInput" :config="$config" style="width: 300px;"/>
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
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Método Pago</th>
                                        <th>Total $</th>
                                        <th>Total bs</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $item)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('h:i A') }}</td>
                                            <td>{{ $item->customer->name }}</td>

                                            @if ($item->cancel_sale)
                                                @if ($item->paid == 0)
                                                    <td><span class="text-danger">ANULADA</span></td>
                                                @else
                                                    <td><span class="text-danger">DEVOLUCIÓN</span></td>
                                                @endif
                                            @else
                                                @if ($item->paid == 0)
                                                    <td><span>Pendiente de pago</span></td>
                                                @else
                                                    <td><span class="text-success">Pagada</span></td>
                                                @endif
                                            @endif
                                            @if (isset($item->methodPayment))
                                                <td><span>{{ @$item->methodPayment->name }}</span></td>
                                            @else
                                                <td><span class="text-danger">Pendiente por pagar</span></td>
                                            @endif
                                            <td>{{ $item->total }}</td>
                                            <td>{{ $item->total_bs }}</td>
                                            <td>
                                                <div>
                                                    @if ($item->invoices && $item->invoices->count() > 0)
                                                        <small>Factura: #{{ $item->invoices[0]->uuid }}</small>
                                                    @else
                                                        @if ($item->deliveryNotes && $item->deliveryNotes->count() > 0)
                                                            @if ($item->paid == 1)
                                                                <small>Nota Entrega:
                                                                    #{{ $item->deliveryNotes[0]->uuid }}</small>
                                                            @else
                                                                <small>Nota Despacho:
                                                                    #{{ $item->deliveryNotes[0]->uuid }}</small>
                                                            @endif
                                                        @else
                                                            <small></small>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div><a href="{{ route('admin.sucursal.sales.history.show', $item) }}">Ver
                                                        detalles</a></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="table2_info" role="status" aria-live="polite">Mostrando 1
                                a 10 de {{ $sales->total() }} registros</div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="table2_paginate">
                                {{ $sales->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
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
