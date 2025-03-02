<?php
// index.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Fundify</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
       <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
       <ul class="navbar-nav ml-auto">
          <li class="nav-item">
             <a class="nav-link" href="login.php">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
             <a class="nav-link" href="register.php">Registrarse</a>
          </li>
       </ul>
    </div>
</nav>

<!-- Jumbotron -->
<div class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4">Bienvenido a Fundify</h1>
        <p class="lead">La plataforma de financiamiento colectivo para apoyar fundaciones y campañas solidarias.</p>
        <hr class="my-4">
        <p>Únete a nuestra comunidad y comienza a hacer la diferencia hoy mismo.</p>
        <a class="btn btn-primary btn-lg" href="register.php" role="button">Regístrate</a>
        <a class="btn btn-secondary btn-lg" href="login.php" role="button">Inicia Sesión</a>
    </div>
</div>

<!-- Sección para Fundaciones -->
<div class="container my-5 text-center">
    <p class="lead">¿Eres una fundación?</p>
    <a href="register_fundacion.php" class="btn btn-info btn-lg">Regístrate como Fundación</a>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
