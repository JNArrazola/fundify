<?php
// view_fundacion.php
require_once 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$fundacion_id = $_GET['id'];
// Obtener datos de la fundación
$sql = "SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'ong'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fundacion_id]);
$fundacion = $stmt->fetch();

if (!$fundacion) {
    echo "Fundación no encontrada.";
    exit;
}

// Obtener campañas activas de la fundación
$sql = "SELECT * FROM campanas WHERE id_usuario = ? AND estado = 'activa'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fundacion_id]);
$campanas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($fundacion['nombre']); ?> - Campañas</title>
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

    .btn-success {
      background-color: #06d6a0;
      border-color: #06d6a0;
    }

    .btn-success:hover {
      background-color: #05c091;
      border-color: #05c091;
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
        <li class="nav-item"><a href="home.php" class="nav-link">Inicio</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Cerrar Sesión</a></li>
      <?php else: ?>
        <li class="nav-item"><a href="login.php" class="nav-link">Iniciar Sesión</a></li>
        <li class="nav-item"><a href="register.php" class="nav-link">Registrarse</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<div class="container">
  <h1>Campañas de <?php echo htmlspecialchars($fundacion['nombre']); ?></h1>
  <?php if(empty($campanas)): ?>
    <p>No hay campañas activas en este momento.</p>
  <?php else: ?>
  <div class="row">
    <?php foreach($campanas as $campana): ?>
      <div class="col-md-6">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($campana['titulo']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($campana['descripcion']); ?></p>
            <p class="card-text"><strong>Meta:</strong> $<?php echo $campana['meta']; ?></p>
            <p class="card-text"><strong>Monto Actual:</strong> $<?php echo $campana['monto_actual']; ?></p>
            <a href="donate.php?id=<?php echo $campana['id']; ?>" class="btn btn-success">Donar</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div class="text-center mt-4">
    <a href="home.php" class="btn btn-secondary">Volver a Inicio</a>
  </div>
</div>
</body>
</html>
