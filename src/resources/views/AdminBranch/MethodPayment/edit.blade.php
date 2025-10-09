@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar Método de pago </h1>
    <small>{{ $methodPayment->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.method.payment.update', $methodPayment) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $methodPayment->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del método de pago"
                        fgroup-class="col-md-4" value="{{ $methodPayment->name }}" />
                    <div class="col-md-4">
                        <label for="currency">Moneda</label>
                        {{ Form::select('currency', ['bs' => 'bs', 'usd' => 'usd'], $methodPayment->currency, ['class' => 'select2 form-control']) }}
                    </div>
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.method.payment.index') }}" class="btn-sm mr-3 btn-default"
                        type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
            });
        });
    </script>
@stop
