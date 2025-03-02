<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
    <p>Aquí podrás consultar tu historial de donaciones, puntos acumulados y campañas apoyadas.</p>
    <a href="logout.php" class="btn btn-secondary">Cerrar Sesión</a>
</div>
</body>
</html>
