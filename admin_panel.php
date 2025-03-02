<?php
// admin_panel.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

// Obtener usuarios
$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll();
// Obtener campañas con el nombre de la fundación creadora
$sql = "SELECT c.*, u.nombre AS fundacion FROM campanas c JOIN usuarios u ON c.id_usuario = u.id";
$campanas = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Panel de Administración</h2>
    <h3>Usuarios Registrados</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Tipo de Usuario</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo $usuario['tipo_usuario']; ?></td>
                <td><?php echo $usuario['fecha_registro']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h3>Campañas</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Fundación</th>
                <th>Meta</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($campanas as $campana): ?>
            <tr>
                <td><?php echo $campana['id']; ?></td>
                <td><?php echo htmlspecialchars($campana['titulo']); ?></td>
                <td><?php echo htmlspecialchars($campana['fundacion']); ?></td>
                <td><?php echo $campana['meta']; ?></td>
                <td><?php echo $campana['estado']; ?></td>
                <td>
                    <a href="edit_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="delete_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta campaña?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="logout.php" class="btn btn-secondary">Cerrar Sesión</a>
</div>
</body>
</html>
