<?php
// admin_panel.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

// Obtener todos los usuarios (incluyendo fundaciones)
$users = $pdo->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC")->fetchAll();

// Obtener todas las campañas junto con el nombre de la fundación que la creó
$sql = "SELECT c.*, u.nombre AS fundacion 
        FROM campanas c 
        JOIN usuarios u ON c.id_usuario = u.id 
        ORDER BY c.fecha_inicio DESC";
$campaigns = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="admin_panel.php">Fundify Admin</a>
    <div class="collapse navbar-collapse">
       <ul class="navbar-nav ml-auto">
           <li class="nav-item">
             <a class="nav-link" href="logout.php">Cerrar Sesión</a>
           </li>
       </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Panel de Administración</h2>
    
    <!-- Gestión de Usuarios -->
    <h3>Gestión de Usuarios</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Fecha de Registro</th>
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
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Gestión de Campañas -->
    <h3>Gestión de Campañas</h3>
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
    
    <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>
</body>
</html>
