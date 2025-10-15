<div class="preloader flex-column justify-content-center align-items-center">

    {{-- Preloader logo --}}
    {{-- <img src="{{ asset('storage/'.session('branch')->logo) }}"
        class="{{ config('adminlte.preloader.img.effect', 'animation__shake') }}"
        alt="{{ config('adminlte.preloader.img.alt', 'AdminLTE Preloader Image') }}"
        width="{{ config('adminlte.preloader.img.width', 60) }}"
        height="{{ config('adminlte.preloader.img.height', 60) }}"> --}}
    <div class="spinner"></div>

</div>
<style>
    .spinner {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 9px solid;
        border-color: #dbdcef;
        border-right-color: #474bff;
        animation: spinner-d3wgkg 1s infinite linear;
    }

    @keyframes spinner-d3wgkg {
        to {
            transform: rotate(1turn);
        }
    }
</style>
