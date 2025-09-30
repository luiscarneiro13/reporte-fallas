@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    {{-- @vite('resources/js/sell.js') --}}
    <h1>Vender</h1>
@stop

@section('content')
    <div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <label for="customer_id">Cliente</label>
                            <a href="#" data-toggle="modal" data-target="#modalAddCustomer" class="small-box-footer">
                                <i class="fas fa-plus-circle"></i></a>
                            {{ Form::select('customer_id', [], null, ['placeholder' => 'Seleccione', 'class' => 'select2 form-control', 'id' => 'customer_id']) }}
                        </div>
                    </div>
                    <div class="col-md-6 float-right">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Cliente:</th>
                                    <td>Luis Carneiro</td>
                                </tr>
                                <tr>
                                    <th>Teléfono:</th>
                                    <td>04248807465</td>
                                </tr>
                                <tr>
                                    <th>Total a pagar:</th>
                                    <td>$20.20 - bs 700</td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <x-adminlte-button class="btn-sm float-right" type="button" label="Finalizar venta"
                                            theme="primary" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <label for="brand_id">Artículos</label>
                            <a href="#" data-toggle="modal" data-target="#modalAddCustomer"
                                class="small-box-footer"><i class="fas fa-plus-circle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Marca</th>
                                <th class="text-center" style="width: 5%">Cant.</th>
                                <th class="text-center" style="width: 5%">P.U $</th>
                                <th class="text-center" style="width: 5%">P.U Bs</th>
                                <th class="text-center" style="width: 5%">SubTotal</th>
                                <th>&nbsp;</th>
                            </tr>


                            <tr>
                                <td>1.</td>
                                <td>Update software</td>
                                <td>Update software</td>
                                <td>Update software</td>
                                <td>
                                    <input class="" type="text" name="" id="" value="1">
                                </td>
                                <td class="text-center">10.10</td>
                                <td class="text-center">10.10</td>
                                <td class="text-center"><strong>10.10</strong></td>
                                <td>
                                    <a href="#" class="text-danger"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Update software</td>
                                <td>Update software</td>
                                <td>Update software</td>
                                <td>
                                    <input class="" type="text" name="" id="" value="1">
                                </td>
                                <td class="text-center">10.10</td>
                                <td class="text-center">10.10</td>
                                <td class="text-center"><strong>10.10</strong></td>
                                <td>
                                    <a href="#" class="text-danger"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- <div id="sell"></div> --}}
@stop
