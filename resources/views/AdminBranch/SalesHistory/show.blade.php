@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')

    @vite('resources/js/finalizeSell.js')


    <div class="row">
        <div class="col-md-6">

            <h1>Detalles de la venta</h1>
            <small>
                @if (request()->has('back_url'))
                    <a href="{{ request()->query('back_url') }}">
                        << Volver atrás </a>
                        @else
                            <a href="{{ route('admin.sucursal.sales.history.index') }}">
                                << Volver atrás </a>
                @endif
            </small>
        </div>
        <div class="col-md-6">
            @if (!$sale->cancel_sale)
                <form class="formEliminar" action="{{ route('admin.sucursal.sales.history.destroy', $sale) }}" method="post">
                    @csrf
                    @method('delete')
                    <x-adminlte-button class="btn-sm float-right text-danger font-weight-bold" type="submit"
                        label="{{ $sale->paid == 1 ? 'Devolución' : 'Anular' }}" theme="default" icon="fas fa-trash" />
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Datos de la venta</h5>
                </div>
                <div class="col-md-6">
                    @if ($sale->paid == 0 && !$sale->cancel_sale)
                        <span class="text-danger font-weight-bolder float-right">Pendiente de pago</span>
                    @endif
                    @if ($sale->paid == 0 && $sale->cancel_sale)
                        <span class="text-danger font-weight-bolder float-right">Anulada</span>
                    @endif
                    @if ($sale->paid == 1 && !$sale->cancel_sale)
                        <span class="text-success font-weight-bolder float-right">Pagada</span>
                    @endif
                    @if ($sale->paid == 1 && $sale->cancel_sale)
                        <span class="text-danger font-weight-bolder float-right">Devuelta</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 bg-gray-light m-1">
                    <div>Código de venta: {{ $sale->uuid }}</div>
                    <div>Cliente: {{ $sale->customer->full_name }}</div>
                    <div>Total artículos: {{ $sale->details->count() }}</div>
                    <div>Método de pago:
                        {{ isset($sale->methodPayment->name) ? $sale->methodPayment->name : 'Pendiente por pagar' }}
                    </div>
                </div>
                <div class="col-md-2 bg-gray-light m-1">
                    <div>Fecha: {{ date('d-m-Y', strtotime($sale->created_at)) }}</div>
                    <div>Hora: {{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</div>
                    <div>Tasa: Bs. {{ $sale->rate }}</div>
                    <div>Total: <strong>$ {{ round($sale->total_bs / $sale->rate, 2) }}</strong></div>
                    <div>Total: <strong>Bs. {{ round($sale->total_bs, 2) }}</strong></div>
                </div>
                @if ($sale->paymentMixed)
                    <div class="col-md-4 bg-gray-light m-1">
                        PAGO MIXTO: <br>

                        @if (isset($sale->paymentMixed->bolivares_efectivo))
                            BOLIVARES EN EFECTIVO: <strong>Bs. {{ $sale->paymentMixed->bolivares_efectivo }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->dolares_efectivo))
                            DOLARES EN EFECTIVO: <strong>Bs. {{ $sale->paymentMixed->dolares_efectivo }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->pago_movil))
                            PAGO MOVIL: <strong>Bs. {{ $sale->paymentMixed->pago_movil }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->biopago))
                            BIOPAGO: <strong>Bs. {{ $sale->paymentMixed->biopago }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->punto_venta_venezuela))
                            PUNTO DE VENTA VENEZUELA: <strong>Bs. {{ $sale->paymentMixed->punto_venta_venezuela }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->punto_venta_banesco))
                            PUNTO DE VENTA BANESCO: <strong>Bs. {{ $sale->paymentMixed->punto_venta_banesco }}</strong><br>
                        @endif

                        @if (isset($sale->paymentMixed->zelle))
                            ZELLE: <strong>Bs. {{ $sale->paymentMixed->bolivares_efectivo }}</strong><br>
                        @endif

                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="finalizeSell"></div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5>Productos o servicios</h5>
            @include('AdminBranch.SalesHistory.saleDetails', ['details' => $sale->details])
        </div>
    </div>

@stop

@php
    if (env('APP_ENV') == 'local') {
        $impresora = env('IMPRESORA_LOCAL_NOTA_ENTREGA');
    } else {
        $impresora = env('IMPRESORA_PRODUCCION_NOTA_ENTREGA');
    }
@endphp

@section('js')
    <script>
        window.methodPayments = @json($methodPayments);
        window.sale = @json($sale);
        window.branchId = {{ session('branch')->id }};
        window.dailyRate = {{ session('dailyRate') }};
        window.averageRate = {{ session('averageRate') }};
        window.tax = {{ session('tax') }};
        window.impresora = "{{ $impresora }}";
        window.back_url = "{{ request()->query('back_url') }}"
    </script>
@stop
