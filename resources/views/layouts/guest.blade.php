<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Discipline') }} - @yield('title', 'Login')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <main class="min-vh-100 d-flex align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4 p-md-5">
                                <div class="text-center mb-4">
                                    <a href="/" class="text-decoration-none">
                                        <h1 class="h3 text-primary fw-bold">School Discipline</h1>
                                    </a>
                                    <p class="text-muted small">Management System</p>
                                </div>

                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
