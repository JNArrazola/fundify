<?php
// campaign_history.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Obtener todas las campañas de la fundación (sin filtrar por b_logico)
$sql = "SELECT * FROM campanas WHERE id_usuario = ? ORDER BY fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$campanas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Campañas - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .navbar {
      background-color: #2b2d42 !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link {
      color: #ffffff !important;
    }

    .container {
      margin-top: 50px;
      margin-bottom: 50px;
    }

    h2 {
      color: #2b2d42;
      font-weight: 700;
      text-align: center;
      margin-bottom: 30px;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    .card-title {
      font-weight: bold;
      font-size: 1.3rem;
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

    .text-danger {
      font-weight: 500;
      color: #e63946 !important;
    }

    .text-success {
      font-weight: 500;
      color: #2a9d8f !important;
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
      <h2>Historial de Campañas</h2>

      <?php if(empty($campanas)): ?>
          <p class="text-center">No tienes campañas registradas.</p>
      <?php else: ?>
          <?php foreach($campanas as $campana): ?>
              <div class="card mb-4">
                  <div class="card-body">
                      <h4 class="card-title"><?php echo htmlspecialchars($campana['titulo']); ?></h4>
                      <p class="card-text"><?php echo htmlspecialchars($campana['descripcion']); ?></p>
                      <p class="card-text">
                          <strong>Meta:</strong> $<?php echo $campana['meta']; ?> &nbsp;
                          <strong>Monto Actual:</strong> $<?php echo $campana['monto_actual']; ?>
                      </p>
                      <p class="card-text">
                          <strong>Estado:</strong> <?php echo $campana['estado']; ?> <br>
                          <strong>Inicio:</strong> <?php echo $campana['fecha_inicio']; ?> &nbsp;
                          <strong>Fin:</strong> <?php echo $campana['fecha_fin']; ?>
                      </p>

                      <?php if($campana['b_logico'] == 0): ?>
                          <p class="text-danger">Campaña deshabilitada</p>
                          <a href="reactivate_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-success btn-sm">Reactivar Campaña</a>
                      <?php else: ?>
                          <p class="text-success">Campaña activa</p>
                      <?php endif; ?>
                  </div>
              </div>
          <?php endforeach; ?>
      <?php endif; ?>

      <div class="text-center mt-4">
          <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
      </div>
  </div>
</body>
</html>
