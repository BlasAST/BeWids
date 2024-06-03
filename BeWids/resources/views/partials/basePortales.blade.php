<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeWids-Portal</title>
    <script src="@yield('rutaJs')"></script>
    <script src="@yield('rutaJs2')"></script>
    @vite('public/css/tailwindBase.css')
</head>
<body class="fondoBewids">
    @yield('contenido')
</body>
</html>