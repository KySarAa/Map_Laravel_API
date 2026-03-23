<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgrIA - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 700;
        }

        /* Modes d'affichage */
        body.ui-desktop {
            font-size: 0.95rem;
        }

        body.ui-mobile {
            font-size: 1.05rem;
        }

        body.ui-mobile .btn {
            padding: 0.7rem 1.1rem;
            font-size: 1rem;
        }

        body.ui-mobile .card {
            border-radius: 18px;
        }

        body.ui-mobile .form-control,
        body.ui-mobile .form-select {
            padding: 0.6rem 0.9rem;
            font-size: 1rem;
        }
    </style>
    @yield('styles')
</head>

<body class="{{ session('ui_mode', 'desktop') === 'mobile' ? 'ui-mobile' : 'ui-desktop' }}">
    @if(!View::hasSection('no-header'))
        <nav class="navbar navbar-dark bg-success sticky-top shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <span>AgrIA</span>
                    <span class="text-white-50 small d-none d-sm-inline">@yield('title')</span>
                </a>
                @auth
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('options') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2"
                           style="border-radius: 999px;">
                            <span class="d-inline-block" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15A1.65 1.65 0 0 0 20 13.6a1.65 1.65 0 0 0-.33-1l2-3.46a1 1 0 0 0-.37-1.37l-2.12-1.22a1 1 0 0 0-1.45.36l-2 3.46a1.7 1.7 0 0 0-.95 0l-2-3.46a1 1 0 0 0-1.45-.36L4.7 7.8a1 1 0 0 0-.37 1.37l2 3.46a1.65 1.65 0 0 0 0 2l-2 3.46a1 1 0 0 0 .37 1.37l2.12 1.22a1 1 0 0 0 1.45-.36l2-3.46a1.7 1.7 0 0 0 .95 0l2 3.46a1 1 0 0 0 1.45.36l2.12-1.22a1 1 0 0 0 .37-1.37Z">
                                    </path>
                                </svg>
                            </span>
                            <span class="d-none d-sm-inline">Options</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link text-white p-0"
                                style="font-size: 1.35rem; text-decoration: none;" title="Déconnexion">
                                &#x2715;
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>
    @endif

    <div class="container pb-5">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>