<div class="barra">
    <p>Hola: <span><a class="perfil" href="/perfil"><?php echo $nombre ?? ''; ?></a></span></p>
    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php if(isset($_SESSION['admin'])){ ?>
    <div class="barra-servicios">
        <?php if($_SERVER['PATH_INFO'] !== '/admin'): ?>
            <a href="/admin" class="boton">Ver Citas</a>
        <?php endif; ?>

        <?php if($_SERVER['PATH_INFO'] !== '/servicios'): ?>
            <a href="/servicios" class="boton">Ver Servicios</a>
        <?php endif; ?>

        <?php if($_SERVER['PATH_INFO'] !== '/servicios/crear'): ?>
            <a href="/servicios/crear" class="boton">Nuevo Servicio</a>
        <?php endif; ?>
    </div>
<?php } ?>
