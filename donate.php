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
// Variables para conservar valores en caso de error
$monto    = "";
$cardNum  = "";
$expMonth = "";
$expYear  = "";
$cvv      = "";
$cardName = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto    = trim($_POST['monto']);
    $cardNum  = trim($_POST['card_number']);
    $expMonth = trim($_POST['exp_month']);
    $expYear  = trim($_POST['exp_year']);
    $cvv      = trim($_POST['cvv']);
    $cardName = trim($_POST['card_name']);
    
    // Validar monto
    if (empty($monto) || !is_numeric($monto) || $monto <= 0) {
        $errors[] = "Debe ingresar un monto válido.";
    }
    // Validar número de tarjeta (simplemente que sea numérico y tenga entre 13 y 16 dígitos)
    if (empty($cardNum) || !ctype_digit($cardNum) || strlen($cardNum) < 13 || strlen($cardNum) > 16) {
        $errors[] = "Debe ingresar un número de tarjeta válido (13 a 16 dígitos).";
    }
    // Validar mes de expiración
    if (empty($expMonth) || !ctype_digit($expMonth) || (int)$expMonth < 1 || (int)$expMonth > 12) {
        $errors[] = "Debe ingresar un mes de expiración válido (01-12).";
    }
    // Validar año de expiración (suponiendo dos dígitos o cuatro, aquí lo tratamos como dos dígitos)
    if (empty($expYear) || !ctype_digit($expYear) || strlen($expYear) != 2) {
        $errors[] = "Debe ingresar un año de expiración válido (por ejemplo, 23 para 2023).";
    }
    // Validar CVV (3 o 4 dígitos)
    if (empty($cvv) || !ctype_digit($cvv) || (strlen($cvv) != 3 && strlen($cvv) != 4)) {
        $errors[] = "Debe ingresar un CVV válido (3 o 4 dígitos).";
    }
    // Validar nombre del titular
    if (empty($cardName)) {
        $errors[] = "Debe ingresar el nombre del titular de la tarjeta.";
    }
    
    if (empty($errors)) {
        // Aquí se simula el procesamiento del pago
        // En un escenario real se integraría una pasarela de pago
        
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
  <style>
    /* Estilos simples para simular una tarjeta de crédito */
    .credit-card-form {
      max-width: 500px;
      margin: auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #f9f9f9;
    }
  </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="dashboard.php">Fundify</a>
</nav>
<div class="container my-5">
  <h1 class="mb-4">Donar a: <?php echo htmlspecialchars($campana['titulo']); ?></h1>
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
  
  <!-- Formulario de donación estilo tarjeta de crédito -->
  <div class="credit-card-form">
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
  
  <a href="view_fundacion.php?id=<?php echo $campana['id_usuario']; ?>" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
