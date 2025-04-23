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
    <style>
        body {
            background-color: #f4f6fa;
            color: #2c2c2c;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            max-width: 800px;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        }

        h2 {
            color: #2b2d42;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .navbar {
            background-color: #2b2d42 !important;
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #ffffff !important;
        }

        .info-block {
            background-color: #e9ecef;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .btn-secondary {
            background-color: #adb5bd;
            border-color: #adb5bd;
        }

        .btn-secondary:hover {
            background-color: #868e96;
            border-color: #868e96;
            color: white;
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
    <h2><?php echo htmlspecialchars($campaign['titulo']); ?></h2>
    <div class="info-block">
        <p class="mb-2"><span class="info-label">Descripción:</span><br><?php echo nl2br(htmlspecialchars($campaign['descripcion'])); ?></p>
    </div>
    <div class="info-block">
        <p class="mb-1"><span class="info-label">Fundación:</span> <?php echo htmlspecialchars($campaign['fundacion']); ?></p>
        <p class="mb-1"><span class="info-label">Estado:</span> <?php echo htmlspecialchars($campaign['estado']); ?></p>
    </div>
    <div class="info-block">
        <p class="mb-1"><span class="info-label">Meta:</span> $<?php echo number_format($campaign['meta'], 2); ?></p>
        <p class="mb-1"><span class="info-label">Monto Actual:</span> $<?php echo number_format($campaign['monto_actual'], 2); ?></p>
    </div>
    <div class="info-block">
        <p class="mb-1"><span class="info-label">Fecha de Inicio:</span> <?php echo date('d/m/Y', strtotime($campaign['fecha_inicio'])); ?></p>
        <p class="mb-0"><span class="info-label">Fecha de Fin:</span> <?php echo date('d/m/Y', strtotime($campaign['fecha_fin'])); ?></p>
    </div>
    <div class="text-center mt-4">
        <a href="my_supported_campaigns.php" class="btn btn-secondary">Volver</a>
    </div>
</div>
</body>
</html>
