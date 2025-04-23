<?php
// edit_campaign.php
session_start();
// Permitir acceso solo si el usuario es 'ong' o 'admin'
if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_usuario'] != 'ong' && $_SESSION['tipo_usuario'] != 'admin')) {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_campaigns.php");
    exit;
}

$campaignId = $_GET['id'];
$user_id    = $_SESSION['user_id'];
$userType   = $_SESSION['tipo_usuario'];

// Obtener la campaña según el tipo de usuario
if ($userType == 'ong') {
    $sql = "SELECT * FROM campanas WHERE id = ? AND id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$campaignId, $user_id]);
} else {
    $sql = "SELECT * FROM campanas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$campaignId]);
}
$campaign = $stmt->fetch();

if (!$campaign) {
    echo "Campaña no encontrada o no tienes permiso para editarla.";
    exit;
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo       = trim($_POST['titulo']);
    $descripcion  = trim($_POST['descripcion']);
    $meta         = trim($_POST['meta']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];
    
    if (empty($titulo))        $errors[] = "El título es requerido.";
    if (empty($descripcion))   $errors[] = "La descripción es requerida.";
    if (empty($meta) || !is_numeric($meta)) $errors[] = "La meta debe ser un número.";
    if (empty($fecha_inicio))  $errors[] = "La fecha de inicio es requerida.";
    if (empty($fecha_fin))     $errors[] = "La fecha de fin es requerida.";
    
    if (empty($errors)) {
        if ($userType == 'ong') {
            $sql_update = "UPDATE campanas SET titulo = ?, descripcion = ?, meta = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ? AND id_usuario = ?";
            $params = [$titulo, $descripcion, $meta, $fecha_inicio, $fecha_fin, $campaignId, $user_id];
        } else {
            $sql_update = "UPDATE campanas SET titulo = ?, descripcion = ?, meta = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?";
            $params = [$titulo, $descripcion, $meta, $fecha_inicio, $fecha_fin, $campaignId];
        }
        $stmt_update = $pdo->prepare($sql_update);
        if ($stmt_update->execute($params)) {
            $success = "Campaña actualizada correctamente.";
            $stmt = $pdo->prepare($userType == 'ong' 
                ? "SELECT * FROM campanas WHERE id = ? AND id_usuario = ?" 
                : "SELECT * FROM campanas WHERE id = ?");
            $stmt->execute($userType == 'ong' ? [$campaignId, $user_id] : [$campaignId]);
            $campaign = $stmt->fetch();
        } else {
            $errors[] = "Error al actualizar la campaña.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Campaña - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6fa;
        }
        .container {
            margin-top: 60px;
            max-width: 700px;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        h2 {
            font-weight: bold;
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
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="dashboard.php">Fundify</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="manage_campaigns.php">Gestionar Campañas</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Editar Campaña</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="edit_campaign.php?id=<?= $campaignId ?>" method="POST">
        <div class="form-group">
            <label for="titulo">Título de la Campaña:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($campaign['titulo']) ?>" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($campaign['descripcion']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta">Meta de Recaudación:</label>
            <input type="number" step="0.01" class="form-control" id="meta" name="meta" value="<?= htmlspecialchars($campaign['meta']) ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($campaign['fecha_inicio']) ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($campaign['fecha_fin']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Campaña</button>
        <a href="manage_campaigns.php" class="btn btn-secondary ml-2">Volver</a>
    </form>
</div>
</body>
</html>
