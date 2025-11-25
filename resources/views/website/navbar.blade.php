<style>
    .site-menu > li > a {
        font-size: 18px !important;   /* Ajusta el tamaño */
        color: #000 !important;       /* Negro */
    }

    /* Si también quieres que los submenús (dropdown) sean negros */
    .site-menu .dropdown li a {
        font-size: 17px !important;
        color: #000 !important;
    }
</style>


<div class="site-navbar-wrap bg-white">
    <div class="site-navbar-top">
        <div class="container py-2">
            <div class="row align-items-center">
                <div class="col-6">
                    <a href="#" class="p-2 pl-0"><span class="icon-twitter"></span></a>
                    <a href="#" class="p-2 pl-0"><span class="icon-facebook"></span></a>
                    <a href="#" class="p-2 pl-0"><span class="icon-linkedin"></span></a>
                    <a href="#" class="p-2 pl-0"><span class="icon-instagram"></span></a>
                </div>
                <div class="col-6">
                    <div class="d-flex ml-auto">
                        <a href="#" class="d-flex align-items-center ml-auto mr-4">
                            <span class="icon-phone mr-2"></span>
                            <span class="d-none d-md-inline-block">info@servicioscasmar.com</span>
                        </a>
                        <a href="#" class="d-flex align-items-center">
                            <span class="icon-envelope mr-2"></span>
                            <span class="d-none d-md-inline-block">+58 0489.485.65.74</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-navbar">
        <div class="container py-1">
            <div class="row align-items-center">
                <div class="col-2">
                    <h2 class="mb-0 site-logo">
                        <img height="100px" src="{{ asset('logo.webp') }}" alt="">
                    </h2>
                </div>
                <div class="col-10">
                    <nav class="site-navigation text-right font-weight-bold" role="navigation">
                        <div class="container">
                            <div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3"><a href="#"
                                    class="site-menu-toggle js-menu-toggle"><span
                                        class="icon-menu h3"></span></a></div>

                            <ul class="site-menu js-clone-nav d-none d-lg-block text-black">
                                <li><a href="#">Inicio</a></li>
                                <li class="has-children">
                                    <a href="#">Nosotros</a>
                                    <ul class="dropdown">
                                        <li><a href="#mision">Misión</a></li>
                                        <li><a href="#vision">Visión</a></li>
                                        <li><a href="#valores">Valores</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Servicios</a></li>
                                <li><a href="#">Contacto</a></li>
                                <li><a href="{{ url('/login') }}">Usuario</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .site-navbar-wrap {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 999;
        /* Asegura que esté por encima de otros elementos */
    }
</style>
