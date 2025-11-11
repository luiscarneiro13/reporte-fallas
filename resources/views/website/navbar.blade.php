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
    <div class="site-navbar bg-light">
        <div class="container py-1">
            <div class="row align-items-center">
                <div class="col-2">
                    <h2 class="mb-0 site-logo">
                        <img height="60px" src="{{ asset('logo.webp') }}" alt="">
                    </h2>
                </div>
                <div class="col-10">
                    <nav class="site-navigation text-right" role="navigation">
                        <div class="container">
                            <div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3"><a href="#"
                                    class="site-menu-toggle js-menu-toggle text-black"><span
                                        class="icon-menu h3"></span></a></div>

                            <ul class="site-menu js-clone-nav d-none d-lg-block">
                                <li><a href="#">Inicio</a></li>
                                <li class="has-children">
                                    <a href="#">Empresa</a>
                                    <ul class="dropdown arrow-top">
                                        <li><a href="#">Misión</a></li>
                                        <li><a href="#">Visión</a></li>
                                        <li><a href="#">Valores</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Servicios</a></li>
                                <li><a href="#">Contacto</a></li>
                                <li><a href="{{ url("/login") }}">Usuario</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

</div>
