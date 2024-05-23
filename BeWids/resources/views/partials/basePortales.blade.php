<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="@yield('rutaJs')"></script>
    @vite('public/css/tailwindBase.css')
</head>
<body class="fondoBewids">
    <div class="contenedor h-screen grid grid-cols-12 grid-rows-12" >
    @yield('contenido')
    </div>
</body>
</html>