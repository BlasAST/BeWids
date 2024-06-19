<header>
    <div class="cabecera">
        @if (Request::is('perfil'))
            <img class="icoHome" src="{{ asset('imagenes/imagenesBase/home.svg')}}">
        @else
        <span></span>

        @endif
        <div class="logo" style="cursor: pointer"></div>
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