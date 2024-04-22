@include('ini');

</head>
<body>
    <div class="botones">
        <h1>NUEVO EN BEWIDS?!</h1>
        <p>Inicia sesión o crea una cuenta BeWids para poder disfrutar de nuestras funcionalidades y empezar a organizarte. A que espperas!</p>
        <button>INICIAR SESIÓN</button>
        <button>CREAR CUENTA</button>
    </div>
    <div class="inicio">
        <h1>INICIAR SESIÓN</h1>
        <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
        <form action="{{route('sesion')}}" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="pass">Contraseña</label>
            <input type="password" name="pass" placeholder="Indica tu contraseña">
            <a href="">He olvidado mi contraseña</a>
            <p>¿No tienes cuenta? <a href="">Registrate</a></p>
            <input type="submit" name="inicio" value="INICIAR SESIÓN">
        </form>
    </div>
    <div class="registro">
        <h1>CREAR CUENTA</h1>
        <p>Crea una cuenta con nosotros para descubrir los servicios que ofrecemos</p>
        <form action="{{route('sesion')}}" method="POST">
            <label for="user">Usuario</label>
            <input type="text" name="user" placeholder="Indica tu usuario">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="email2">Repetir Email</label>
            <input type="email" name="email2" placeholder="Repite tu correo electrónico">
            <label for="pass">Contraseña</label>
            <input type="password" name="pass" placeholder="Indica tu contraseña">
            <label for="pass2">Repetir Contraseña</label>
            <input type="password" name="pass2" placeholder="Tepite tu contraseña">
            <a href="">¿Ya tienes cuenta?</a>
            <input type="submit" name="registro" value="CREAR CUENTA">
        </form>
    </div>