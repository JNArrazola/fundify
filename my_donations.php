<?php
// my_donations.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Obtener el historial de donaciones del usuario
$sql = "SELECT d.*, c.titulo AS campaign_title 
        FROM donaciones d
        JOIN campanas c ON d.id_campana = c.id
        WHERE d.id_usuario = ? 
        ORDER BY d.fecha_donacion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$donations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Historial de Donaciones - Fundify</title>
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
      <h2>Mi Historial de Donaciones</h2>
      <?php if(empty($donations)): ?>
          <p>No has realizado donaciones.</p>
      <?php else: ?>
          <table class="table table-bordered">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Campaña</th>
                      <th>Monto</th>
                      <th>Fecha</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach($donations as $donation): ?>
                      <tr>
                          <td><?php echo $donation['id']; ?></td>
                          <td><?php echo htmlspecialchars($donation['campaign_title']); ?></td>
                          <td>$<?php echo $donation['monto']; ?></td>
                          <td><?php echo $donation['fecha_donacion']; ?></td>
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      <?php endif; ?>
      <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
  </div>
</body>
</html>
