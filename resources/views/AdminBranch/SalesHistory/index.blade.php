@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content')
    @php
        $headers = ['Fecha', 'Hora', 'Cliente', 'Estado', 'Total $', 'Total bs', ''];
    @endphp
    <x-base-data-table-date title="Historial de ventas" :items="$sales" :headers="$headers">
        <x-slot name="body">
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
                    {{-- @if (isset($item->methodPayment))
                        <td><span>{{ @$item->methodPayment->name }}</span></td>
                    @else
                        <td><span class="text-danger">Pendiente por pagar</span></td>
                    @endif --}}
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
                        <div>
                            <a
                                href="{{ route('admin.sucursal.sales.history.show', $item->id) }}?back_url={{ url()->current() }}">
                                Ver detalles
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-base-data-table-date>
@stop
@section('customjs')

@stop
