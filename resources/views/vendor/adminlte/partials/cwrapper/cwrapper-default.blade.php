@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@if ($layoutHelper->isLayoutTopnavEnabled())
    @php($def_container_class = 'container')
@else
    @php($def_container_class = 'container-fluid')
@endif

{{-- Default Content Wrapper --}}
<div class="content-wrapper {{ config('adminlte.classes_content_wrapper', '') }}">

    {{-- Content Header --}}
    @hasSection('content_header')
        <div class="content-header">
            <div class="{{ config('adminlte.classes_content_header') ?: $def_container_class }}">
                @yield('content_header')
                <x-alert />
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="content">
        <div class="{{ config('adminlte.classes_content') ?: $def_container_class }}">
            @stack('content')
            @yield('content')
        </div>
    </div>

    <div class="separacion-movil-600"></div>
</div>

<style>
    /*
    Esta Media Query aplica los estilos a pantallas con un ancho MÁXIMO de 991px.
    Esto cubre típicamente móviles y tablets (Bootstrap usa 992px como punto de quiebre para 'md').
    */
    @media (max-width: 991px) {
        .separacion-movil-600 {
            height: 600px !important;
            /* ¡Importante! Asegura que se aplique */
            margin: 0;
            padding: 0;
            display: block;
        }
    }

    /* Para pantallas grandes, esta clase no tendrá efecto (height: 0 o simplemente se omite) */
    @media (min-width: 992px) {
        .separacion-movil-600 {
            height: 0 !important;
        }
    }
</style>
