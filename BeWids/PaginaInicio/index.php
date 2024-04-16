    <?php
    include_once '../vistas/cabecera.php';
    ?>
    <link rel="stylesheet" href="estilos/estiloInicio.css">
</head>

<body>
    <header>
        <div class="cabecera">
            <button class="hamburgesa"></button>
            <div></div>
            <img src="../imagenes/ajustes2.png" alt="">
        </div>
    </header>
    <main>
        <div class="contenedor">
            <button class="bperfil">
                <h1>Perfil</h1>
            </button>
            <div class="perfil">
                <h1>!Bienvenido [Nombre de usuario]!</h1>
                <div class="parte1">
                    <img src="../imagenes/alvaro.jpg" alt="">
                    <div>
                        <h1>Nombre de usuario</h1>
                        <h3>Nombre: Nombre Fijo</h3>
                        <h3>Edad: 20</h3>
                        <h3>Mis actividades:<a href="">ver más</a></h3>
                        <p>Descripcion Breve: Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam nulla,
                            natus corrupti quod delectus porro nihil eum sed incidunt vero cum culpa quis aliquid,
                            beatae sit obcaecati minus, repellat reiciendis!</p>
                    </div>
                </div>

                <p>La siguiente información no estará disponibles para los usuarios por defecto</p>
                <div class="noVisible">
                    <h6>Numero de grupos a los que perteneces</h6>
                    <p>Fecha de Nacimiento</p>
                    <p>Numero de contacto</p>
                    <p>Provincia</p>
                </div>
            </div>
            <button class="bsesiones">
                <h1>Sesiones</h1>
            </button>
            <div class="sesiones">
                <h2>Sesiones activas</h2>
                <ul>
                    <li>Asede</li>
                    <li>Vacaciones</li>
                    <li>Findes Fiesta</li>
                    <li>Cansones</li>
                </ul>
                <h2>Sesiones anteriores</h2>
            </div>
            <!-- Perfil sin Ajustes -->
            <!-- Sesiones -->
            <!-- ¿Por defecto abierto perfil, al presionar -->
            <!-- sobre sesiones hacer perfil como boton de abrir -->
            <!-- y sesiones se abre?? -->
        </div>
    </main>
    <?php
    include_once '../vistas/footer.php';
    ?>