@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Asignación de roles y permisos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <p>Rol: <strong>{{ $role->name }}</strong></p>
        </div>
        <div class="card-body">
            <h5>Lista de Permisos</h5>
            {!! Form::model($role, ['route' => ['roles.update', $role], 'method' => 'put']) !!}
            <div class="row mb-5">
                @foreach ($permissions as $item)
                    <div class="col-2">
                        <label>
                            {!! Form::checkbox('permissions[]', $item->id, $role->hasPermissionTo($item->id) ?: false, ['class' => 'mr-1']) !!}
                            {{ $item->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('roles.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                icon="fas fa-lg fa-save">Cancelar</a>
            {!! form::submit('Asignar permisos', ['class' => 'btn btn-primary mt-2 float-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop
