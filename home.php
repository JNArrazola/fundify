<?php
// home.php
require_once 'db.php';
session_start();

// Consulta para obtener las fundaciones
$sql = "SELECT * FROM usuarios WHERE tipo_usuario = 'ong'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fundaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Explorar Fundaciones - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      <?php if(isset($_SESSION['user_id'])): ?>
        <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Cerrar Sesión</a></li>
      <?php else: ?>
        <li class="nav-item"><a href="login.php" class="nav-link">Iniciar Sesión</a></li>
        <li class="nav-item"><a href="register.php" class="nav-link">Registrarse</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
<div class="container my-5">
  <h1 class="mb-4">Explorar Fundaciones</h1>
  <div class="row">
    <?php foreach($fundaciones as $fundacion): ?>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($fundacion['nombre']); ?></h5>
            <p class="card-text">Contacto: <?php echo htmlspecialchars($fundacion['email']); ?></p>
            <a href="view_fundacion.php?id=<?php echo $fundacion['id']; ?>" class="btn btn-primary">Ver Campañas</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
</div>
</body>
</html>
