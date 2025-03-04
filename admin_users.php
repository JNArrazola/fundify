<?php
// admin_users.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$searchQuery = "";
$params = [];

// Siempre excluimos usuarios de tipo 'admin'
$baseWhere = " WHERE tipo_usuario != 'admin' ";

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchQuery = trim($_GET['q']);
    $whereClause = " AND (id LIKE ? OR nombre LIKE ? OR email LIKE ? OR tipo_usuario LIKE ?)";
    $paramSearch = "%{$searchQuery}%";
    $params = [$paramSearch, $paramSearch, $paramSearch, $paramSearch];
} else {
    $whereClause = "";
}

$sql = "SELECT * FROM usuarios " . $baseWhere . $whereClause . " ORDER BY fecha_registro DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - Fundify Admin</title>
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
    <h2>Gestión de Usuarios</h2>
    <form class="form-inline mb-3" method="GET" action="admin_users.php">
      <input type="text" name="q" class="form-control mr-2" placeholder="Buscar usuarios..." value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Tipo</th>
          <th>Fecha de Registro</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['tipo_usuario']; ?></td>
            <td><?php echo $user['fecha_registro']; ?></td>
            <td><?php echo ($user['b_logico'] == 0) ? 'Desactivado' : 'Activo'; ?></td>
            <td>
              <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
              <a href="toggle_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info" onclick="return confirm('¿Seguro que deseas <?php echo ($user['b_logico'] == 1) ? 'desactivar' : 'activar'; ?> este usuario?');">
                <?php echo ($user['b_logico'] == 1) ? 'Desactivar' : 'Activar'; ?>
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
