<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Discipline') }} - {{ $title ?? 'Login' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                min-height: 100vh;
            }
        </style>
    </head>
    <body>
        <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                        <!-- Login Card -->
                        <div class="card shadow-lg border-0" style="border-radius: 16px;">
                            <div class="card-body p-4 p-md-5">
                                <!-- Logo & Title -->
                                <div class="text-center mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #e0e7ff; border-radius: 16px;">
                                        <i class="bi bi-shield-check text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h1 class="h4 fw-bold text-dark mb-1">School Discipline</h1>
                                    <p class="text-muted small mb-0">Sistem Monitoring Kedisiplinan Siswa</p>
                                </div>

                                {{ $slot }}
                            </div>
                        </div>

                        <!-- Demo Accounts Info -->
                        <div class="text-center mt-4">
                            <p class="text-white small mb-2 opacity-75">Akun Demo (password: <code class="text-white">password</code>)</p>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <span class="badge bg-danger bg-opacity-75">Super Admin</span>
                                <code class="small text-white-50">super@example.com</code>
                                <span class="badge bg-primary bg-opacity-75">Admin</span>
                                <code class="small text-white-50">admin@smk1jkt.sch.id</code>
                                <span class="badge bg-secondary bg-opacity-75">Operator</span>
                                <code class="small text-white-50">operator@smk1jkt.sch.id</code>
                                <span class="badge bg-info bg-opacity-75">Guru</span>
                                <code class="small text-white-50">guru.tkj@smk1jkt.sch.id</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
