<!DOCTYPE html>
<html lang="en">

<head>
    <title>Servicios Generales Casmar</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,700,900|Roboto+Mono:300,400,500">
    <link rel="stylesheet" href="{{ asset('website/fonts/icomoon/style.css') }}">

    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap-datepicker.css') }}">


    <link rel="stylesheet" href="{{ asset('website/fonts/flaticon/font/flaticon.css') }}">

    <link rel="stylesheet" href="{{ asset('website/css/aos.css') }}">

    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">

</head>

<body>

    <div class="site-wrap">

        <div class="site-mobile-menu">
            <div class="site-mobile-menu-header">
                <div class="site-mobile-menu-close mt-3">
                    <span class="icon-close2 js-menu-toggle"></span>
                </div>
            </div>
            <div class="site-mobile-menu-body"></div>
        </div> <!-- .site-mobile-menu -->

        @include('website.navbar')

        @include('website.slider')

        {{-- @include('website.number_speak') --}}

        {{-- @include('website.our_speciality') --}}

        @include('website.mision')

        @include('website.vision')

        @include('website.valores')

        @include('website.servicios')

        {{-- @include('website.lets_grow') --}}

        {{-- @include('website.reviews') --}}

        {{-- @include('website.our_video') --}}


        {{-- <div class="promo py-5 bg-warning" data-aos="fade">
            <div class="container text-center">
                <h2 class="d-block mb-0 font-weight-light text-white">
                    <a href="#" class="text-black d-block">Contáctanos</a>
                </h2>
            </div>
        </div> --}}

        @include('website.footer')
    </div>

    <script src="{{ asset('website/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('website/js/popper.min.js') }}"></script>
    <script src="{{ asset('website/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('website/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('website/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('website/js/aos.js') }}"></script>

    <script src="{{ asset('website/js/main.js') }}"></script>

</body>

</html>
