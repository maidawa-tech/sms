<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Management System')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard-css/dashboard-style.css') }}">

    {{-- VITE (app.js loads Chart.js, Alpine, etc.) --}}
    @vite(['resources/js/app.js'])

    <!-- ================= LOADER STYLES ================= -->
    <style>
        /* Fullscreen Loader Overlay */
        #page-loader {
            position: fixed;
            inset: 0;
            background: #ffffff;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }

        #page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-circle-44 {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .loader-circle-44 > div {
            position: absolute;
            width: 2px;
            height: 15px;
            background-color: #679767;
            opacity: 0.05;
            animation: fadeit 0.8s linear infinite;
        }

        .loader-circle-44 > .bar-1  { transform: rotate(0deg) translate(0, -12px);   animation-delay: 0.05s; }
        .loader-circle-44 > .bar-2  { transform: rotate(22.5deg) translate(0, -12px);animation-delay: 0.1s; }
        .loader-circle-44 > .bar-3  { transform: rotate(45deg) translate(0, -12px);  animation-delay: 0.15s; }
        .loader-circle-44 > .bar-4  { transform: rotate(67.5deg) translate(0, -12px);animation-delay: 0.2s; }
        .loader-circle-44 > .bar-5  { transform: rotate(90deg) translate(0, -12px);  animation-delay: 0.25s; }
        .loader-circle-44 > .bar-6  { transform: rotate(112.5deg) translate(0, -12px);animation-delay: 0.3s; }
        .loader-circle-44 > .bar-7  { transform: rotate(135deg) translate(0, -12px); animation-delay: 0.35s; }
        .loader-circle-44 > .bar-8  { transform: rotate(157.5deg) translate(0, -12px);animation-delay: 0.4s; }
        .loader-circle-44 > .bar-9  { transform: rotate(180deg) translate(0, -12px); animation-delay: 0.45s; }
        .loader-circle-44 > .bar-10 { transform: rotate(202.5deg) translate(0, -12px);animation-delay: 0.5s; }
        .loader-circle-44 > .bar-11 { transform: rotate(225deg) translate(0, -12px); animation-delay: 0.55s; }
        .loader-circle-44 > .bar-12 { transform: rotate(247.5deg) translate(0, -12px);animation-delay: 0.6s; }
        .loader-circle-44 > .bar-13 { transform: rotate(270deg) translate(0, -12px); animation-delay: 0.65s; }
        .loader-circle-44 > .bar-14 { transform: rotate(292.5deg) translate(0, -12px);animation-delay: 0.7s; }
        .loader-circle-44 > .bar-15 { transform: rotate(315deg) translate(0, -12px); animation-delay: 0.75s; }
        .loader-circle-44 > .bar-16 { transform: rotate(337.5deg) translate(0, -12px);animation-delay: 0.8s; }

        @keyframes fadeit {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
    <!-- ================================================= -->
</head>

<body>
    <!-- ================= PAGE LOADER ================= -->
    <div id="page-loader">
        <div class="loader-circle-44">
            <div class="bar-1"></div><div class="bar-2"></div><div class="bar-3"></div><div class="bar-4"></div>
            <div class="bar-5"></div><div class="bar-6"></div><div class="bar-7"></div><div class="bar-8"></div>
            <div class="bar-9"></div><div class="bar-10"></div><div class="bar-11"></div><div class="bar-12"></div>
            <div class="bar-13"></div><div class="bar-14"></div><div class="bar-15"></div><div class="bar-16"></div>
        </div>
    </div>
    <!-- =============================================== -->

    {{-- Header (Navbar) --}}
    @include('includes.header')

    {{-- Sidebar --}}
    @include('includes.sidebar')

    {{-- Page Content --}}
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    {{-- Scripts --}}
    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dashboard-js/dashboard.js') }}"></script>

    @yield('scripts') 

    {{-- Toasts --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('warning') }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="toast align-items-center text-bg-info border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('info') }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Toast Init -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toast').forEach(toastEl => {
                new bootstrap.Toast(toastEl, { delay: 5000 }).show();
            });
        });

        // Hide loader when page fully loaded
        window.addEventListener('load', () => {
            const loader = document.getElementById('page-loader');
            if (loader) {
               setTimeout(() => {
                  loader.classList.add('hidden');
               }, 700);
            }
        });
    </script>
</body>
</html>
