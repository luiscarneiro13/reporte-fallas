@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Asignar Rol a Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <p>Usuario: <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="card-body">
            <h5>Lista de Roles</h5>
            {!! Form::model($user, ['route' => ['assign_role.update', $user], 'method' => 'put']) !!}
            <div class="row">
                @foreach ($roles as $item)
                    <div class="col-2">
                        <label>
                            {!! Form::checkbox('roles[]', $item->id, $user->hasAnyRole($item->id) ?: false, ['class' => 'mr-1']) !!}
                            {{ $item->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            {!! form::submit('Asignar Roles', ['class' => 'btn btn-primary mt-2 float-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop
