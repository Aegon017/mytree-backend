<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>{{env('PROJECT_TITLE')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Stylesheets -->
    <link href="{{ asset('frontEnd/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('frontEnd/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontEnd/css/responsive.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Caslon+Text:wght@400;700&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="icon" href="{{ asset('frontEnd/images/fav-icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
</head>

<body>
    <div class="page-wrapper">
        @include('layouts.master_commons.header')
        @yield('content')
        @include('layouts.master_commons.footer')
    </div>

    <a href="https://wa.me/918106166857" class="whatsapp" target="_blank">
        <i class="fab fa-whatsapp my-whatsapp"></i>
    </a>

    <script src="{{ asset('frontEnd/js/jquery.js') }}"></script>
    <script src="{{ asset('frontEnd/js/appear.js') }}"></script>
    <script src="{{ asset('frontEnd/js/owl.js') }}"></script>
    <script src="{{ asset('frontEnd/js/wow.js') }}"></script>
    <script src="{{ asset('frontEnd/js/odometer.js') }}"></script>
    <script src="{{ asset('frontEnd/js/mixitup.js') }}"></script>
    <script src="{{ asset('frontEnd/js/knob.js') }}"></script>
    <script src="{{ asset('frontEnd/js/popper.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/parallax-scroll.js') }}"></script>
    <script src="{{ asset('frontEnd/js/parallax.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/tilt.jquery.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/script.js') }}"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>

    @section('script')
    @show
</body>

</html>
