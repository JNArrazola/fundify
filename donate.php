<?php
// donate.php
require_once 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$campana_id = $_GET['id'];

// Obtener datos de la campaña (incluyendo el nombre de la fundación)
$sql = "SELECT c.*, u.nombre AS fundacion, u.id AS fundacion_id FROM campanas c JOIN usuarios u ON c.id_usuario = u.id WHERE c.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$campana_id]);
$campana = $stmt->fetch();

if (!$campana) {
    echo "Campaña no encontrada.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto = trim($_POST['monto']);
    if (empty($monto) || !is_numeric($monto)) {
        $errors[] = "Debe ingresar un monto válido.";
    } else {
        // Insertar la donación
        $sql = "INSERT INTO donaciones (id_campana, id_usuario, monto) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$campana_id, $_SESSION['user_id'] ?? null, $monto]);
        // Actualizar el monto acumulado de la campaña
        $sql = "UPDATE campanas SET monto_actual = monto_actual + ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$monto, $campana_id]);
        header("Location: view_fundacion.php?id=" . $campana['id_usuario']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Donar a <?php echo htmlspecialchars($campana['titulo']); ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
</nav>
<div class="container my-5">
  <h1>Donar a: <?php echo htmlspecialchars($campana['titulo']); ?></h1>
  <p>Fundación: <?php echo htmlspecialchars($campana['fundacion']); ?></p>
  <?php if(!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach($errors as $error): ?>
      <li><?php echo htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
  <form action="donate.php?id=<?php echo $campana_id; ?>" method="POST">
    <div class="form-group">
      <label for="monto">Monto a donar:</label>
      <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
    </div>
    <button type="submit" class="btn btn-success">Donar</button>
  </form>
  <a href="view_fundacion.php?id=<?php echo $campana['id_usuario']; ?>" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
