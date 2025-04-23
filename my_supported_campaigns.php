<?php
// my_supported_campaigns.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Consultar las campañas a las que el usuario ha donado, agrupando las donaciones
$sql = "SELECT c.id, c.titulo, c.descripcion, c.meta, c.monto_actual, 
               SUM(d.monto) AS total_donado, COUNT(d.id) AS num_donaciones
        FROM donaciones d
        JOIN campanas c ON d.id_campana = c.id
        WHERE d.id_usuario = ?
        GROUP BY c.id, c.titulo, c.descripcion, c.meta, c.monto_actual
        ORDER BY MAX(d.fecha_donacion) DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$supported_campaigns = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Campañas Apoyadas - Fundify</title>
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

    table th {
      background-color: #6c63ff;
      color: white;
    }

    table td {
      vertical-align: middle;
    }

    .btn-sm {
      padding: 4px 10px;
      font-size: 0.875rem;
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
      <h2>Mis Campañas Apoyadas</h2>
      <?php if(empty($supported_campaigns)): ?>
          <p>Aún no has apoyado ninguna campaña.</p>
      <?php else: ?>
          <table class="table table-bordered">
              <thead>
                  <tr>
                      <th>ID Campaña</th>
                      <th>Título</th>
                      <th>Descripción</th>
                      <th>Meta</th>
                      <th>Monto Actual</th>
                      <th>Total Donado</th>
                      <th>Número de Donaciones</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach($supported_campaigns as $campaign): ?>
                      <tr>
                          <td><?php echo $campaign['id']; ?></td>
                          <td><?php echo htmlspecialchars($campaign['titulo']); ?></td>
                          <td><?php echo htmlspecialchars($campaign['descripcion']); ?></td>
                          <td>$<?php echo $campaign['meta']; ?></td>
                          <td>$<?php echo $campaign['monto_actual']; ?></td>
                          <td>$<?php echo $campaign['total_donado']; ?></td>
                          <td><?php echo $campaign['num_donaciones']; ?></td>
                          <td>
                              <a href="view_campaign.php?id=<?php echo $campaign['id']; ?>" class="btn btn-sm btn-primary">Ver Campaña</a>
                          </td>
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      <?php endif; ?>
      <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
  </div>
</body>
</html>
