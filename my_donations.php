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
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .container {
      margin-top: 50px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    h2 {
      color: #2b2d42;
      margin-bottom: 20px;
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

    table th {
      background-color: #6c63ff;
      color: white;
    }

    table td {
      vertical-align: middle;
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
      <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
  </div>
</body>
</html>
