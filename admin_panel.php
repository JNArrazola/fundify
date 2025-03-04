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
  <div class="container mt-5">
    <h2>Panel de Administración</h2>
    <p>Seleccione una opción para gestionar:</p>
    <div class="row">
      <div class="col-md-6">
        <a href="admin_users.php" class="btn btn-primary btn-block">Gestión de Usuarios</a>
      </div>
      <div class="col-md-6">
        <a href="admin_campaigns.php" class="btn btn-info btn-block">Gestión de Campañas</a>
      </div>
    </div>
  </div>
</body>
</html>
