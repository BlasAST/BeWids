<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo')</title>
    <link rel="stylesheet" href="{{asset('css/estilosBase.css')}}">
    <link rel="stylesheet" href="@yield('rutaEstilos')">
</head>
<body>
    @yield('contenido')
    
</body>
</html>