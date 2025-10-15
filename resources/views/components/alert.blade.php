@if (session() && session('state'))
    @switch(session('state'))
        @case('success')
            <div class="row">
                <x-adminlte-alert theme="success" title="Success"
                    class="col-md-12">{{ session('message') ? session('message') : 'Cambio exitoso!' }}</x-adminlte-alert>
            </div>
        @break

        @case('error')
            <div class="row">
                <x-adminlte-alert theme="danger" title="Error!"
                    class="col-md-12">{{ session('message') ? session('message') : 'Ah ocurrido un error!' }}</x-adminlte-alert>
            </div>
        @break

        @default
    @endswitch
@endif
