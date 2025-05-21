<!DOCTYPE html>
<html lang="id">
@include('mitrabps.headerTemp')
</head>
<body>
    <x-sidebar></x-sidebar>

    <header>
        <h1>Aplikasi Saya</h1>
        <nav>
            <a href="/">Beranda</a> |
            <a href="/tentang">Tentang</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Aplikasi Saya</p>
    </footer>

</body>
</html>
