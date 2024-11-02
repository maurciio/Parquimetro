<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg" style="background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(252,176,69,1) 100%);">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="fa-solid fa-map-location"></i> Geoparquimetro</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        
        <?php if (isset($_SESSION['rut'])): ?>
          <?php if ($_SESSION['role'] == 'administrador'): ?>
            <li class="nav-item">
              <a class="nav-link" href="../view/index.php">Dashboard <i class="fa-solid fa-chart-line"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../view/usuarios.php">Usuarios <i class="fa-solid fa-users"></i></a>
            </li>
          <?php endif; ?>

          <?php if ($_SESSION['role'] == 'administrador' || $_SESSION['role'] == 'operador'): ?>
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

