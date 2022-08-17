<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('iCLOP') }}</title>

        <!-- Scripts -->
        <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Sarabun" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('lte/dist/css/adminlte.min.css')}}" <link rel="stylesheet"
            href="https://unpkg.com/aos@next/dist/aos.css" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
        <link href="{{ asset('css/auth.css') }}" rel="stylesheet">

        <!-- Icon -->
        <link rel="icon" href="{{ asset('storage/assets/images/icon.png') }}">
    </head>

    <body>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse py-4" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mx-auto">
                            <a class="navbar-brand" href="/">
                                <img src="{{ asset('storage/assets/images/logo.png') }}" alt="" height="50px">
                            </a>
                        </ul>
                        <!-- Center Side Of Navbar -->
                        <ul class="navbar-nav mx-auto navbar-custom">
                            <li class="nav-item">
                                <a class="nav-link" href="/">{{ __('Home') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('About Us') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Tutorials') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Examples') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ __('Contact Us') }}</a>
                            </li>
                        </ul>
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav mx-auto">
                            <!-- Authentication Links -->
                            @guest
                            @if (Route::has('login'))
                            <li class="nav-item btn-register-login my-2">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @endif

                            @if (Route::has('register'))
                            <li class="nav-item btn-register-login my-2">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                            @endif
                            @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main>
                @yield('content')
            </main>
        </div>
        <footer class="footerWelcome">
            <div class="footerLinks">
                <div class="footerIcon" data-aos="fade-down" data-aos-duration="1000">
                    <img src="{{ asset('storage/assets/images/logo.png') }}" alt="" height="50px">
                    <p>{{ __('your way to become a better programmer </>') }}
                    </p>
                </div>
                <div class="customFooterLinks">
                    <div class="ps-3" data-aos="fade-down" data-aos-duration="800">
                        <ul class="navbar-nav mx-auto navbar-custom">
                            <li class="nav-item">
                                <a class="nav-link" href="/">{{ __('Home') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('About Us') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Tutorials') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Examples') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Contact Us') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="ps-4" data-aos="fade-down" data-aos-duration="900">
                        <ul class="navbar-nav mx-auto navbar-custom">
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Android Studio') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Python') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('C++') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Flutter') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('JavaScript') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="ps-5" data-aos="fade-down" data-aos-duration="1000">
                        <ul class="navbar-nav mx-auto navbar-custom">
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Policy') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Privacy') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Licence') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">{{ __('Help') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="copyRights">
                    <p>Copyright 2022 © icode.com</p>
                </div>
            </div>
            <div class="footerContactUs">
                <img src="{{ asset('storage/assets/images/contactUs.png') }}" alt="" data-aos="fade-right"
                    data-aos-duration="1000">
                <div class="ContactUsForm" data-aos="fade-up" data-aos-duration="1000">
                    <form action="">
                        <h1>Send us a message</h1>
                        <input type="text" placeholder="Subject">
                        <textarea name="" id="" cols="30" rows="10" placeholder="Your message..."></textarea>
                        <button>Send</button>
                    </form>
                </div>
            </div>
        </footer>
    </body>
    <script>
        AOS.init();
    </script>
    <script src="{{asset('lte/plugins/jquery/jquery.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('lte/dist/js/adminlte.min.js')}}"></script>


</html>
