<h1 class="nombre-pagina">Nuevo Servicio</h1>
<p class="descripcion-pagina">Crea un nuevo servicio para el salon.</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
?>


<form action="/servicios/crear" method="POST" class="formulario">

    <?php include_once __DIR__ . '/formulario.php'; ?>

    <input type="submit" class="boton-blanco" value="Crear">
</form>