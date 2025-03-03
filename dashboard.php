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
    <?php if($_SESSION['tipo_usuario'] == 'ong'): ?>
        <h2>Panel de Gestión de Fundación</h2>
        <p>Desde aquí podrás gestionar tus campañas, peticiones de donaciones y actualizar la información de tu fundación.</p>
        <div class="mb-3">
            <a href="create_campaign.php" class="btn btn-primary">Crear Nueva Campaña</a>
            <a href="manage_campaigns.php" class="btn btn-info">Gestionar Peticiones de Donaciones</a>
            <a href="manage_foundation.php" class="btn btn-secondary">Administrar Mi Fundación</a>
        </div>
    <?php else: ?>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
        <p>Aquí podrás consultar tu historial de donaciones, puntos acumulados y campañas apoyadas.</p>
        <a href="home.php" class="btn btn-primary">Explorar Fundaciones</a>
    <?php endif; ?>
</div>
</body>
</html>
