<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$puntos = 0;
if ($_SESSION['tipo_usuario'] == 'donante') {
    $stmt = $pdo->prepare("SELECT puntos FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    if ($userData) {
        $puntos = $userData['puntos'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6fa;
            color: #2c2c2c;
        }

        h2 {
            color: #2b2d42;
            margin-bottom: 20px;
        }

        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
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

        .btn-info {
            background-color: #48cae4;
            border-color: #48cae4;
            color: white;
        }

        .btn-info:hover {
            background-color: #2ec4b6;
            border-color: #2ec4b6;
        }

        .btn-warning {
            background-color: #ffd166;
            border-color: #ffd166;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #fcbf49;
            border-color: #fcbf49;
            color: #212529;
        }

        .btn-danger {
            background-color: #e63946;
            border-color: #e63946;
        }

        .btn-danger:hover {
            background-color: #d62828;
            border-color: #d62828;
        }

        .btn-success {
            background-color: #06d6a0;
            border-color: #06d6a0;
        }

        .btn-success:hover {
            background-color: #05c091;
            border-color: #05c091;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="dashboard.php">Fundify</a>
      <div class="collapse navbar-collapse">
          <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
          </ul>
      </div>
  </nav>

  <div class="container">
    <?php if($_SESSION['tipo_usuario'] == 'admin'): ?>
        <h2>Panel de Administración</h2>
        <p>Bienvenido, administrador. Desde aquí puedes gestionar usuarios, fundaciones y campañas, además de moderar contenido.</p>
        <div class="mb-3">
            <a href="admin_panel.php" class="btn btn-danger">Ir al Panel de Administración</a>
        </div>
    <?php elseif($_SESSION['tipo_usuario'] == 'ong'): ?>
        <h2>Panel de Gestión de Fundación</h2>
        <p>Desde aquí podrás gestionar tus campañas, ver el historial completo y actualizar la información de tu fundación.</p>
        <div class="mb-3">
            <a href="create_campaign.php" class="btn btn-primary">Crear Nueva Campaña</a>
            <a href="manage_campaigns.php" class="btn btn-info">Gestionar Campañas Activas</a>
            <a href="campaign_history.php" class="btn btn-secondary">Historial de Campañas</a>
            <a href="manage_foundation.php" class="btn btn-warning">Administrar Mi Fundación</a>
        </div>
    <?php else: ?>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
        <p>Aquí podrás consultar tu historial de donaciones, tus puntos acumulados, las campañas que has apoyado y canjear recompensas.</p>
        <div class="alert alert-info">
            <strong>Puntos acumulados:</strong> <?php echo $puntos; ?>
        </div>
        <div class="mb-3">
            <a href="my_donations.php" class="btn btn-info">Historial de Donaciones</a>
            <a href="my_supported_campaigns.php" class="btn btn-secondary">Campañas Apoyadas</a>
            <a href="home.php" class="btn btn-primary">Explorar Fundaciones</a>
            <a href="rewards_store.php" class="btn btn-success">Tienda de Recompensas</a>
        </div>
    <?php endif; ?>
  </div>
</body>
</html>
