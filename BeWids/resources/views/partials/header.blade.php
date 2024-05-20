<header>
    <div class="cabecera">
        <button class="hamburgesa"></button>
        <div class="logo"></div>
        <img class="icoPerfil" src="{{ asset('imagenes/imagenesBase/perfil.svg')}}" alt="">
        <div class="ajustes">
            <div>
                <button class="editar">Editar Perfil</button>
                <a href="{{route('cerrarS')}}">Cerrar Sesion</a>
            </div>
        </div>
        <img src="{{asset('imagenes/imagenesBasic/ajustes.png')}}" alt="" class="bajustes">
    </div>

</header>