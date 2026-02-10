<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
    @yield('styles')
</head>

<body>
    @if(!View::hasSection('no-header'))
        <nav class="navbar navbar-dark bg-success sticky-top shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    @yield('title')
                </a>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link text-white p-0"
                            style="font-size: 1.5rem; text-decoration: none;" title="Déconnexion">
                            🚪
                        </button>
                    </form>
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