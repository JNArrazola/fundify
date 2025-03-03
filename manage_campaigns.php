<?php
// manage_campaigns.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Consultar todas las campañas de la fundación actual
$sql = "SELECT * FROM campanas WHERE id_usuario = ? ORDER BY fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$campanas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Campañas - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="dashboard.php">Fundify</a>
    <div class="collapse navbar-collapse">
       <ul class="navbar-nav ml-auto">
           <li class="nav-item">
             <a class="nav-link" href="dashboard.php">Dashboard</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="manage_foundation.php">Administrar Fundación</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="logout.php">Cerrar Sesión</a>
           </li>
       </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Gestionar Mis Campañas</h2>
    <?php if(empty($campanas)): ?>
        <p>No tienes campañas registradas. <a href="create_campaign.php" class="btn btn-primary">Crear Nueva Campaña</a></p>
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
                        <strong>Estado:</strong> <?php echo $campana['estado']; ?> &nbsp;
                        <strong>Inicio:</strong> <?php echo $campana['fecha_inicio']; ?>,
                        <strong>Fin:</strong> <?php echo $campana['fecha_fin']; ?>
                    </p>
                    <!-- Sección de Donaciones -->
                    <h5>Donaciones:</h5>
                    <?php
                    // Consultar las donaciones de esta campaña
                    $sql_donations = "SELECT d.*, u.nombre AS donante FROM donaciones d 
                                      JOIN usuarios u ON d.id_usuario = u.id 
                                      WHERE d.id_campana = ? ORDER BY d.fecha_donacion DESC";
                    $stmt_donations = $pdo->prepare($sql_donations);
                    $stmt_donations->execute([$campana['id']]);
                    $donaciones = $stmt_donations->fetchAll();
                    ?>
                    <?php if(empty($donaciones)): ?>
                        <p>No se han realizado donaciones para esta campaña.</p>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Donante</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($donaciones as $donacion): ?>
                                    <tr>
                                        <td><?php echo $donacion['id']; ?></td>
                                        <td><?php echo htmlspecialchars($donacion['donante']); ?></td>
                                        <td>$<?php echo $donacion['monto']; ?></td>
                                        <td><?php echo $donacion['fecha_donacion']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <!-- Opciones para editar o eliminar la campaña -->
                    <a href="edit_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-warning btn-sm">Editar Campaña</a>
                    <a href="delete_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta campaña?');">Eliminar Campaña</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="create_campaign.php" class="btn btn-primary">Crear Nueva Campaña</a>
    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
</div>
</body>
</html>
