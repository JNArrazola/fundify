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
  <div class="container mt-5">
      <h2>Historial de Campañas</h2>
      <?php if(empty($campanas)): ?>
          <p>No tienes campañas registradas.</p>
      <?php else: ?>
          <?php foreach($campanas as $campana): ?>
              <div class="card mb-3">
                  <div class="card-body">
                      <h4 class="card-title"><?php echo htmlspecialchars($campana['titulo']); ?></h4>
                      <p class="card-text"><?php echo htmlspecialchars($campana['descripcion']); ?></p>
                      <p class="card-text">
                          <strong>Meta:</strong> $<?php echo $campana['meta']; ?>, 
                          <strong>Monto Actual:</strong> $<?php echo $campana['monto_actual']; ?>
                      </p>
                      <p class="card-text">
                          <strong>Estado:</strong> <?php echo $campana['estado']; ?>,
                          <strong>Inicio:</strong> <?php echo $campana['fecha_inicio']; ?>,
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
      <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
  </div>
</body>
</html>
