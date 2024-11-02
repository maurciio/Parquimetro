<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<nav class="navbar navbar-expand-lg  "  style="background: rgb(131,58,180);background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(252,176,69,1) 100%);">
  <div class="container-fluid ">
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav" style="--bs-scroll-height: 100px;">
      
        
          <li class="nav-item">
          <a class="navbar-brand" href="index.php"><i class="fa-solid fa-map-location"></i> Geoparquimetro</a>
          </li>
          <?php if (isset($_SESSION['rut'])):  ?>
          <?php if ($_SESSION['role'] == 'administrador'): ?>
          <li class="nav-item">
              <a class="nav-link" href="dashboard.php">Dashboard <i class="fa-solid fa-chart-line"></i></a>
            </li>
            <?php endif; ?> 
          <?php if ($_SESSION['role'] == 'administrador' || $_SESSION['role'] == 'operador'): ?>
            <li class="nav-item">
              <a class="nav-link" href="operador.php">Cobro <i class="fa-solid fa-receipt"></i></a>
            </li>

            <?php if ($_SESSION['role'] == 'administrador'): ?>
            <li class="nav-item">
              <a class="nav-link" href="../../CRUD/crud.php">Usuarios <i class="fa-solid fa-users"></i></a>
            </li>
            
            
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="?logout=true"> Cerrar Sesión <i class="fa-solid fa-door-open"></i></a>
          </li>
          <?php endif; ?>
          <?php endif; ?>
          
      
          
        
      </ul>
      
    </div>
  </div>
</nav>
</body>
</html>