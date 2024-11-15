<?php
function renderNav()
{
    $isLoggedIn = isset($_SESSION['rut']);
    $rol = $_SESSION['rol'] ?? null;
?>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <?php if ($rol === 'administrador'): ?>
                <a class="navbar-brand" href="../view/mapa.php"><i class="fa-solid fa-map-location"></i> Geoparquimetro</a>
            <?php elseif ($rol === 'operador'): ?>
                <a class="navbar-brand" href="../view/operador.php"><i class="fa-solid fa-map-location"></i> Actualizar</a>
            <?php endif; ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($rol === 'administrador'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../view/dashboard.php">Dashboard <i class="fa-solid fa-chart-line"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../view/usuarios.php">Usuarios <i class="fa-solid fa-users"></i></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($rol === 'administrador' || $rol === 'operador'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../view/operador.php">Cobro <i class="fa-solid fa-receipt"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?logout=true">Cerrar Sesión <i class="fa-solid fa-door-open"></i></a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

<?php
}
?>