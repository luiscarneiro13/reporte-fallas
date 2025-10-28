@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-body">
            <div class="row">

                @if (isset($mostFailingEquipment['equipment_name']))
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <p>Equipo con más índice de fallas</p>
                                <p>{{ $mostFailingEquipment['equipment_name'] }}</p>

                            </div>
                            <div class="icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            {{-- <a href="#" class="small-box-footer">Ver detalles <i
                                    class="fas fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
                @endif
                <!-- ./col -->
                @if ($mostFailReported['reported_by_name'])
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <p>Usuario con más fallas reportadas</p>
                                <p>{{ $mostFailReported['reported_by_name'] }}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                            {{-- <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
                @endif
                <!-- ./col -->
                <div class="col-lg-2 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Fallas activas</p>
                            <h4>{{ $totalActiveFaults }}</h4>
                        </div>
                        {{-- <div class="icon">
                            <i class="fas fa-bug"></i>
                        </div> --}}
                        {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <p>Fallas cerradas</p>
                            <h4>{{ $totalClosedFaults }}</h4>
                        </div>
                        {{-- <div class="icon">
                            <i class="fas fa-bug"></i>
                        </div> --}}
                        {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                    </div>
                </div>
                <!-- ./col -->
                {{-- <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>65</h3>

                            <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div> --}}
                <!-- ./col -->
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <x-chart title="Fallas por equipo" type="bar" :labels="$failuresByEquipment['labels']" :values="$failuresByEquipment['values']"
                        :show-percentages="true" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-chart title="Fallas por división" type="pie" :labels="$failuresByDivision['labels']" :values="$failuresByDivision['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas por proyectos" type="pie" :labels="$failuresByProject['labels']" :values="$failuresByProject['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas por usuario" type="pie" :labels="$failuresByReporter['labels']" :values="$failuresByReporter['values']"
                        :show-percentages="true" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <x-chart title="Fallas por estatus" type="pie" :labels="$failuresByStatus['labels']" :values="$failuresByStatus['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas por status de repuestos" type="pie" :labels="$failuresBySparePartStatus['labels']" :values="$failuresBySparePartStatus['values']"
                        :show-percentages="true" />
                </div>

                <div class="col-md-4">
                    <x-chart title="Fallas abiertas y cerradas" type="pie" :labels="$faultsByStatus['labels']" :values="$faultsByStatus['values']"
                        :show-percentages="true" />
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script></script>
@stop
