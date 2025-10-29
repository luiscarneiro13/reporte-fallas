@extends('adminlte::page') {{-- Si usas AdminLTE --}}

@section('title', 'Acceso Denegado')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="error-page">
                <h2 class="headline text-danger"> 403</h2>
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-danger"></i> Acceso Prohibido.</h3>
                    <p>
                        No cuentas con los permisos necesarios para acceder a esta sección de la aplicación.
                        <br>
                        Contacta a un administrador si crees que esto es un error.
                    </p>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">< Ir atras</a>
                </div>
            </div>
        </div>
    </div>
@stop

