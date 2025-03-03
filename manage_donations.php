<?php
// manage_donations.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

// Consulta para obtener las donaciones de las campañas de la fundación actual
$sql = "SELECT d.id, d.monto, d.fecha_donacion, c.titulo AS campana, u.nombre AS donante
        FROM donaciones d
        JOIN campanas c ON d.id_campana = c.id
        JOIN usuarios u ON d.id_usuario = u.id
        WHERE c.id_usuario = ?
        ORDER BY d.fecha_donacion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$donaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Donaciones - Fundify</title>
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
             <a class="nav-link" href="logout.php">Cerrar Sesión</a>
           </li>
       </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Gestionar Peticiones de Donaciones</h2>
    <?php if(empty($donaciones)): ?>
        <p>No hay donaciones registradas para tus campañas.</p>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Campaña</th>
                <th>Donante</th>
                <th>Monto</th>
                <th>Fecha de Donación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($donaciones as $donacion): ?>
            <tr>
                <td><?php echo $donacion['id']; ?></td>
                <td><?php echo htmlspecialchars($donacion['campana']); ?></td>
                <td><?php echo htmlspecialchars($donacion['donante']); ?></td>
                <td>$<?php echo $donacion['monto']; ?></td>
                <td><?php echo $donacion['fecha_donacion']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
</div>
</body>
</html>
