<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BeWids - Error</title>
    <link rel="icon" href="{{ asset('imagenes/imagenesBase/logo.ico') }}" type="image/x-icon">
    @vite('public/css/tailwindBase.css')

</head>
<body class="min-h-[100dvh] flex flex-col items-center justify-center bg-colorCabera ">

    <div class="text-colorDetalles md:pt-20">
        <span style="font-size:max(7vw,48px)">ERROR</span>
        <h2 style="font-size:max(4vw,32px)">Algo ha salido mal......</h2>
        <a href="/" class="text-colorComplem" style="font-size:max(2vw,24px)">Volver</a>
    </div>
    <div class="absolute top-5 w-1/2 h-[250px] fondoError"></div>

    
</body>
</html>