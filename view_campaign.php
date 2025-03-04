<?php
// view_campaign.php
session_start();
require_once 'db.php';

// Verificar que se envíe el ID de la campaña
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$campaignId = $_GET['id'];

// Consulta la campaña y su fundación asociada
$sql = "SELECT c.*, u.nombre AS fundacion 
        FROM campanas c 
        JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$campaignId]);
$campaign = $stmt->fetch();

if (!$campaign) {
    echo "Campaña no encontrada.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Campaña - <?php echo htmlspecialchars($campaign['titulo']); ?></title>
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
    <h2><?php echo htmlspecialchars($campaign['titulo']); ?></h2>
    <p><?php echo htmlspecialchars($campaign['descripcion']); ?></p>
    <p><strong>Meta:</strong> $<?php echo $campaign['meta']; ?></p>
    <p><strong>Monto Actual:</strong> $<?php echo $campaign['monto_actual']; ?></p>
    <p><strong>Fundación:</strong> <?php echo htmlspecialchars($campaign['fundacion']); ?></p>
    <p><strong>Estado:</strong> <?php echo $campaign['estado']; ?></p>
    <p><strong>Fecha de Inicio:</strong> <?php echo $campaign['fecha_inicio']; ?></p>
    <p><strong>Fecha de Fin:</strong> <?php echo $campaign['fecha_fin']; ?></p>
    <a href="my_supported_campaigns.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
