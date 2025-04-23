<?php
// admin_panel.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - Fundify Admin</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #2b2d42 !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link {
      color: #ffffff !important;
    }

    .container {
      margin-top: 60px;
      max-width: 700px;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #2b2d42;
      font-weight: 700;
    }

    .btn-block {
      padding: 15px;
      font-size: 1.1rem;
    }

    .btn + .btn {
      margin-top: 20px;
    }
  </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="admin_panel.php">Fundify Admin</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
    </ul>
  </div>
</nav>

<div class="container">
  <h2>Panel de Administración</h2>
  <p class="text-center">Seleccione una opción para gestionar:</p>
  <div class="d-grid gap-3">
    <a href="admin_users.php" class="btn btn-primary btn-block">Gestión de Usuarios</a>
    <a href="admin_campaigns.php" class="btn btn-info btn-block">Gestión de Campañas</a>
    <a href="admin_fundaciones.php" class="btn btn-success btn-block">Gestión de Fundaciones</a>
  </div>
</div>
</body>
</html>
