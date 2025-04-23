<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Si el usuario no es una fundación (ong), pero es admin, redirige al panel de administración.
if ($_SESSION['tipo_usuario'] != 'ong') {
    if ($_SESSION['tipo_usuario'] == 'admin') {
        header("Location: admin_panel.php");
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM campanas WHERE id_usuario = ? AND b_logico = 1 ORDER BY fecha_inicio DESC";
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
    <style>
        body {
            background-color: #f4f6fa;
            color: #2c2c2c;
        }

        .navbar {
            background-color: #2b2d42 !important;
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #ffffff !important;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        h2 {
            color: #2b2d42;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-weight: bold;
            font-size: 1.3rem;
            color: #2b2d42;
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

        .btn-warning {
            color: #212529;
            background-color: #ffdd57;
            border-color: #ffdd57;
        }

        .btn-warning:hover {
            background-color: #f5c518;
            border-color: #f5c518;
        }

        .btn-danger {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .btn-danger:hover {
            background-color: #e63946;
            border-color: #e63946;
        }

        .table-sm th, .table-sm td {
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

<div class="container">
    <h2>Gestionar Mis Campañas</h2>

    <?php if(empty($campanas)): ?>
        <p class="text-center">No tienes campañas registradas.</p>
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
                        <strong>Inicio:</strong> <?php echo $campana['fecha_inicio']; ?>,
                        <strong>Fin:</strong> <?php echo $campana['fecha_fin']; ?>
                    </p>

                    <!-- Donaciones -->
                    <h5>Donaciones:</h5>
                    <?php
                    $campaignId = $campana['id'];
                    $sql_donations = "SELECT d.*, u.nombre AS donante FROM donaciones d 
                                      JOIN usuarios u ON d.id_usuario = u.id 
                                      WHERE d.id_campana = ? ORDER BY d.fecha_donacion DESC";
                    $stmt_donations = $pdo->prepare($sql_donations);
                    $stmt_donations->execute([$campaignId]);
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

                    <!-- Acciones -->
                    <a href="edit_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-warning btn-sm">Editar Campaña</a>
                    <a href="delete_campaign.php?id=<?php echo $campana['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas deshabilitar esta campaña?');">Deshabilitar Campaña</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="create_campaign.php" class="btn btn-primary mr-2">Crear Nueva Campaña</a>
        <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</div>
</body>
</html>
