<?php
// create_campaign.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Verificar si la fundación está verificada
$sql = "SELECT verificada FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$verificada = $stmt->fetchColumn();

// Si no está verificada, bloquear creación de campañas
if (!$verificada) {
    $bloqueado = true;
} else {
    $bloqueado = false;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$bloqueado) {
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
            $stmt->execute([$user_id, $titulo, $descripcion, $meta, $fecha_inicio, $fecha_fin]);
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
    <style>
      body {
        background-color: #f4f6fa;
        color: #2c2c2c;
      }

      .container {
        margin-top: 50px;
        margin-bottom: 50px;
        max-width: 600px;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      }

      h2 {
        color: #2b2d42;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
      }

      .btn-primary {
        background-color: #6c63ff;
        border-color: #6c63ff;
      }

      .btn-primary:hover {
        background-color: #574fd6;
        border-color: #574fd6;
      }

      .alert-danger {
        background-color: #ffe3e3;
        border-color: #ff6b6b;
        color: #c92a2a;
      }

      .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
      }

      a {
        color: #6c63ff;
      }

      a:hover {
        color: #574fd6;
      }

      label {
        font-weight: 500;
      }
    </style>
</head>
<body>
<div class="container">
    <h2>Crear Nueva Campaña</h2>

    <?php if ($bloqueado): ?>
      <div class="alert alert-warning text-center">
        Tu cuenta aún no ha sido <strong>verificada</strong> por un administrador. No puedes crear campañas hasta que se apruebe tu documentación como fundación ONG.
      </div>
      <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Volver al Dashboard</a>
      </div>
    <?php else: ?>
      <?php if(!empty($errors)): ?>
          <div class="alert alert-danger">
              <ul class="mb-0">
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
          <button type="submit" class="btn btn-primary btn-block">Crear Campaña</button>
      </form>
      <p class="mt-4 text-center"><a href="dashboard.php">← Volver al Panel</a></p>
    <?php endif; ?>
</div>
</body>
</html>
