<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'donante') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

// Obtener puntos del usuario actual
$stmt = $pdo->prepare("SELECT puntos FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch();
$puntos = $usuario ? $usuario['puntos'] : 0;

// Obtener recompensas
$stmt = $pdo->query("SELECT * FROM recompensas WHERE stock > 0 ORDER BY puntos_requeridos ASC");
$recompensas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tienda de Recompensas - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .container {
      margin-top: 50px;
      margin-bottom: 50px;
    }

    .navbar {
      background-color: #2b2d42 !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link {
      color: #ffffff !important;
    }

    h2 {
      color: #2b2d42;
      font-weight: 700;
      margin-bottom: 20px;
      text-align: center;
    }

    .alert-info {
      background-color: #e3f2fd;
      border-color: #90caf9;
      color: #0d47a1;
      font-size: 1.1rem;
      font-weight: 500;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    .card-title {
      font-weight: bold;
      color: #2b2d42;
    }

    .card-text {
      font-size: 0.95rem;
    }

    .btn-success {
      background-color: #06d6a0;
      border-color: #06d6a0;
    }

    .btn-success:hover {
      background-color: #05c091;
      border-color: #05c091;
    }

    .btn-secondary:disabled {
      background-color: #ccc;
      border-color: #ccc;
    }

    .card-img-top {
      max-height: 180px;
      object-fit: cover;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
</nav>

<div class="container">
  <h2>Tienda de Recompensas</h2>
  <div class="alert alert-info text-center">Tienes <strong><?php echo $puntos; ?></strong> puntos disponibles.</div>
  
  <div class="row">
    <?php foreach ($recompensas as $item): ?>
      <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
          <?php if ($item['imagen']): ?>
            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($item['nombre']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($item['descripcion']); ?></p>
            <p><strong><?php echo $item['puntos_requeridos']; ?> puntos</strong></p>
            <?php if ($puntos >= $item['puntos_requeridos']): ?>
              <a href="redeem.php?id=<?php echo $item['id']; ?>" class="btn btn-success btn-block">Canjear</a>
            <?php else: ?>
              <button class="btn btn-secondary btn-block" disabled>Insuficientes puntos</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
