<?php
// redeem.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'donante') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

// Obtener ID de la recompensa
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: rewards_store.php");
    exit;
}
$producto_id = $_GET['id'];

// Obtener datos del producto
$stmt = $pdo->prepare("SELECT * FROM recompensas WHERE id = ?");
$stmt->execute([$producto_id]);
$producto = $stmt->fetch();

if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

// Obtener puntos del usuario
$stmt = $pdo->prepare("SELECT puntos FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch();

$puntos_usuario = $usuario ? $usuario['puntos'] : 0;
$puntos_requeridos = $producto['puntos_requeridos'];

$errors = [];
$success = "";

$direccion = "";
$telefono = "";
$confirmacion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $confirmacion = $_POST['confirmacion'] ?? "";

    if (empty($direccion)) {
        $errors[] = "La dirección es obligatoria.";
    }
    if (empty($telefono)) {
        $errors[] = "El teléfono es obligatorio.";
    }
    if ($confirmacion !== 'on') {
        $errors[] = "Debes confirmar que deseas realizar el canje.";
    }

    if ($puntos_usuario < $puntos_requeridos) {
        $errors[] = "No tienes suficientes puntos para esta recompensa.";
    }

    if (empty($errors)) {
        // Descontar puntos y reducir stock
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET puntos = puntos - ? WHERE id = ?");
            $stmt->execute([$puntos_requeridos, $_SESSION['user_id']]);

            $stmt = $pdo->prepare("UPDATE recompensas SET stock = stock - 1 WHERE id = ?");
            $stmt->execute([$producto_id]);

            // Registrar canje
            $stmt = $pdo->prepare("INSERT INTO canjes (id_usuario, id_recompensa, direccion, telefono) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $producto_id, $direccion, $telefono]);

            $pdo->commit();
            $success = "¡Canje exitoso! Pronto recibirás tu recompensa en la dirección proporcionada.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error al procesar el canje: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Canjear Recompensa - Fundify</title>
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

    .alert-success {
      background-color: #d1e7dd;
      border-color: #badbcc;
      color: #0f5132;
    }

    .alert-danger {
      background-color: #ffe3e3;
      border-color: #ff6b6b;
      color: #c92a2a;
    }

    .form-check-label {
      font-weight: 500;
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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
</nav>

<div class="container">
  <h2>Canjear: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
  <p><strong>Puntos requeridos:</strong> <?php echo $producto['puntos_requeridos']; ?></p>
  <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <a href="rewards_store.php" class="btn btn-success btn-block mt-3">Volver a la Tienda</a>
  <?php else: ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="redeem.php?id=<?php echo $producto_id; ?>" class="mt-4">
      <div class="form-group">
        <label for="direccion">Dirección de envío:</label>
        <textarea name="direccion" id="direccion" class="form-control" rows="3" required><?php echo htmlspecialchars($direccion); ?></textarea>
      </div>
      <div class="form-group">
        <label for="telefono">Teléfono de contacto:</label>
        <input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo htmlspecialchars($telefono); ?>" required>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="confirmacion" name="confirmacion">
        <label class="form-check-label" for="confirmacion">
          Confirmo que deseo canjear esta recompensa.
        </label>
      </div>
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Confirmar Canje</button>
        <a href="rewards_store.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
