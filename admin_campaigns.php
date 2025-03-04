<?php
// admin_campaigns.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$searchQuery = "";
$whereClause = "";
$params = [];
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchQuery = trim($_GET['q']);
    $whereClause = " WHERE c.id LIKE ? OR c.titulo LIKE ? OR u.nombre LIKE ? OR c.estado LIKE ? ";
    $paramSearch = "%{$searchQuery}%";
    $params = [$paramSearch, $paramSearch, $paramSearch, $paramSearch];
}
$sql = "SELECT c.*, u.nombre AS fundacion 
        FROM campanas c 
        JOIN usuarios u ON c.id_usuario = u.id 
        " . $whereClause . " ORDER BY c.fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$campaigns = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Campañas - Fundify Admin</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="admin_panel.php">Fundify Admin</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="admin_panel.php">Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </nav>
  <div class="container mt-5">
    <h2>Gestión de Campañas</h2>
    <form class="form-inline mb-3" method="GET" action="admin_campaigns.php">
      <input type="text" name="q" class="form-control mr-2" placeholder="Buscar campañas..." value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Fundación</th>
          <th>Meta</th>
          <th>Monto Actual</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($campaigns as $campaign): ?>
          <tr>
            <td><?php echo $campaign['id']; ?></td>
            <td><?php echo htmlspecialchars($campaign['titulo']); ?></td>
            <td><?php echo htmlspecialchars($campaign['fundacion']); ?></td>
            <td>$<?php echo $campaign['meta']; ?></td>
            <td>$<?php echo $campaign['monto_actual']; ?></td>
            <td><?php echo ($campaign['b_logico'] == 0) ? 'Deshabilitada' : 'Activa'; ?></td>
            <td>
              <a href="edit_campaign.php?id=<?php echo $campaign['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
              <a href="toggle_campaign.php?id=<?php echo $campaign['id']; ?>" class="btn btn-sm btn-info" onclick="return confirm('¿Seguro que deseas <?php echo ($campaign['b_logico'] == 1) ? 'desactivar' : 'activar'; ?> esta campaña?');">
                <?php echo ($campaign['b_logico'] == 1) ? 'Desactivar' : 'Activar'; ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <a href="admin_panel.php" class="btn btn-secondary mt-3">Volver al Panel</a>
  </div>
</body>
</html>
