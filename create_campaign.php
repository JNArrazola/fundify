<?php
// create_campaign.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo       = trim($_POST['titulo']);
    $descripcion  = trim($_POST['descripcion']);
    $meta         = trim($_POST['meta']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];
    
    if (empty($titulo)) {
        $errors[] = "El título es requerido.";
    }
    if (empty($descripcion)) {
        $errors[] = "La descripción es requerida.";
    }
    if (empty($meta) || !is_numeric($meta)) {
        $errors[] = "La meta debe ser un número.";
    }
    if (empty($fecha_inicio)) {
        $errors[] = "La fecha de inicio es requerida.";
    }
    if (empty($fecha_fin)) {
        $errors[] = "La fecha de fin es requerida.";
    }
    
    if(empty($errors)) {
        $sql = "INSERT INTO campanas (id_usuario, titulo, descripcion, meta, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?, 'activa')";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_SESSION['user_id'], $titulo, $descripcion, $meta, $fecha_inicio, $fecha_fin]);
            header("Location: dashboard.php?campaign_created=1");
            exit;
        } catch(PDOException $e) {
            $errors[] = "Error al crear la campaña: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Campaña - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Crear Nueva Campaña</h2>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="create_campaign.php" method="POST">
        <div class="form-group">
            <label for="titulo">Título de la campaña:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
         <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
         </div>
         <div class="form-group">
            <label for="meta">Meta de recaudación:</label>
            <input type="number" step="0.01" class="form-control" id="meta" name="meta" required>
         </div>
         <div class="form-group">
            <label for="fecha_inicio">Fecha de inicio:</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
         </div>
         <div class="form-group">
            <label for="fecha_fin">Fecha de fin:</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
         </div>
         <button type="submit" class="btn btn-primary">Crear Campaña</button>
    </form>
    <p class="mt-3"><a href="dashboard.php">Volver al Panel</a></p>
</div>
</body>
</html>
