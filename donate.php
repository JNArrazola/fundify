<?php
// donate.php
require_once 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$campana_id = $_GET['id'];

$sql = "SELECT c.*, u.nombre AS fundacion, u.id AS fundacion_id 
        FROM campanas c 
        JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$campana_id]);
$campana = $stmt->fetch();

if (!$campana) {
    echo "Campaña no encontrada.";
    exit;
}

$errors = [];
$monto = $cardNum = $expMonth = $expYear = $cvv = $cardName = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto    = trim($_POST['monto']);
    $cardNum  = trim($_POST['card_number']);
    $expMonth = trim($_POST['exp_month']);
    $expYear  = trim($_POST['exp_year']);
    $cvv      = trim($_POST['cvv']);
    $cardName = trim($_POST['card_name']);

    if (empty($monto) || !is_numeric($monto) || $monto <= 0) {
        $errors[] = "Debe ingresar un monto válido.";
    }
    if (empty($cardNum) || !ctype_digit($cardNum) || strlen($cardNum) < 13 || strlen($cardNum) > 16) {
        $errors[] = "Debe ingresar un número de tarjeta válido (13 a 16 dígitos).";
    }
    if (empty($expMonth) || !ctype_digit($expMonth) || (int)$expMonth < 1 || (int)$expMonth > 12) {
        $errors[] = "Debe ingresar un mes de expiración válido (01-12).";
    }
    if (empty($expYear) || !ctype_digit($expYear) || strlen($expYear) != 2) {
        $errors[] = "Debe ingresar un año de expiración válido (por ejemplo, 23 para 2023).";
    }
    if (empty($cvv) || !ctype_digit($cvv) || (strlen($cvv) != 3 && strlen($cvv) != 4)) {
        $errors[] = "Debe ingresar un CVV válido (3 o 4 dígitos).";
    }
    if (empty($cardName)) {
        $errors[] = "Debe ingresar el nombre del titular de la tarjeta.";
    }

    if (empty($errors)) {
        $usuario_id = $_SESSION['user_id'] ?? null;

        $stmt = $pdo->prepare("INSERT INTO donaciones (id_campana, id_usuario, monto) VALUES (?, ?, ?)");
        $stmt->execute([$campana_id, $usuario_id, $monto]);

        // Actualizar monto acumulado
        $stmt = $pdo->prepare("UPDATE campanas SET monto_actual = monto_actual + ? WHERE id = ?");
        $stmt->execute([$monto, $campana_id]);

        if ($usuario_id) {
            $puntos = floor($monto / 10);
            $stmt = $pdo->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE id = ?");
            $stmt->execute([$puntos, $usuario_id]);
        }

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
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .container {
      margin-top: 50px;
      margin-bottom: 50px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      max-width: 600px;
    }

    .navbar {
      background-color: #2b2d42 !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link {
      color: #ffffff !important;
    }

    .btn-success {
      background-color: #06d6a0;
      border-color: #06d6a0;
    }

    .btn-success:hover {
      background-color: #05c091;
      border-color: #05c091;
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

    .credit-card-form {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    label {
      font-weight: 500;
    }

    .alert-danger ul {
      margin-bottom: 0;
    }

    .text-primary {
      color: #6c63ff !important;
    }

    .display-5 {
      font-size: 2rem;
    }

    h2.h4 {
      color: #2b2d42;
      font-weight: 600;
    }

    .text-muted {
      color: #6c757d !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
</nav>

<div class="container">
  <div class="mb-4 text-center">
    <h1 class="display-5 font-weight-bold text-primary">Donar a</h1>
    <h2 class="h4 mb-1"><?php echo htmlspecialchars($campana['titulo']); ?></h2>
    <p class="text-muted">Fundación: <strong><?php echo htmlspecialchars($campana['fundacion']); ?></strong></p>
  </div>

  <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="credit-card-form mt-4">
    <form action="donate.php?id=<?php echo $campana_id; ?>" method="POST">
      <div class="form-group">
        <label for="monto">Monto a donar:</label>
        <input type="number" step="0.01" class="form-control" id="monto" name="monto" value="<?php echo htmlspecialchars($monto); ?>" required>
      </div>
      <hr>
      <h5>Información de la Tarjeta</h5>
      <div class="form-group">
        <label for="card_number">Número de Tarjeta:</label>
        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234123412341234" value="<?php echo htmlspecialchars($cardNum); ?>" required>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="exp_month">Mes de Expiración:</label>
          <input type="text" class="form-control" id="exp_month" name="exp_month" placeholder="MM" value="<?php echo htmlspecialchars($expMonth); ?>" required>
        </div>
        <div class="form-group col-md-6">
          <label for="exp_year">Año de Expiración:</label>
          <input type="text" class="form-control" id="exp_year" name="exp_year" placeholder="YY" value="<?php echo htmlspecialchars($expYear); ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label for="cvv">CVV:</label>
        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" value="<?php echo htmlspecialchars($cvv); ?>" required>
      </div>
      <div class="form-group">
        <label for="card_name">Nombre del Titular:</label>
        <input type="text" class="form-control" id="card_name" name="card_name" placeholder="Nombre tal como aparece en la tarjeta" value="<?php echo htmlspecialchars($cardName); ?>" required>
      </div>
      <button type="submit" class="btn btn-success btn-block">Donar</button>
    </form>
  </div>

  <div class="text-center mt-4">
    <a href="view_fundacion.php?id=<?php echo $campana['id_usuario']; ?>" class="btn btn-secondary">Volver</a>
  </div>
</div>
</body>
</html>
