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
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .container {
      margin-top: 50px;
      margin-bottom: 50px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    h1 {
      color: #2b2d42;
      margin-bottom: 30px;
      text-align: center;
    }

    .navbar {
      background-color: #2b2d42 !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link {
      color: #ffffff !important;
    }

    .navbar .nav-link:hover {
      color: #adb5bd !important;
    }

    .card {
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      border-radius: 12px;
    }

    .card-title {
      font-weight: bold;
      color: #2b2d42;
    }

    .btn-primary {
      background-color: #6c63ff;
      border-color: #6c63ff;
    }

    .btn-primary:hover {
      background-color: #574fd6;
      border-color: #574fd6;
    }

    .btn-secondary {
      background-color: #adb5bd;
      border-color: #adb5bd;
      color: #212529;
    }

    .btn-secondary:hover {
      background-color: #868e96;
      border-color: #868e96;
      color: white;
    }
  </style>
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

<div class="container">
  <h1>Explorar Fundaciones</h1>
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
  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
  </div>
</div>
</body>
</html>
