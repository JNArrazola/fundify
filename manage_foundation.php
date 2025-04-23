<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Obtener datos actuales
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre    = trim($_POST['nombre']);
    $email     = trim($_POST['email']);
    $contacto  = trim($_POST['contacto']);
    $direccion = trim($_POST['direccion']);

    $imagen   = $_FILES['imagen'] ?? null;
    $documento = $_FILES['documento'] ?? null;

    $titular  = trim($_POST['titular']);
    $tarjeta  = trim($_POST['tarjeta']);
    $exp_mes  = trim($_POST['exp_mes']);
    $exp_anio = trim($_POST['exp_anio']);
    $cvv      = trim($_POST['cvv']);

    if (empty($nombre)) $errors[] = "El nombre es requerido.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email es inválido.";

    if (empty($errors)) {
        // Guardar archivos nuevos si fueron enviados
        $imgPath = $user['imagen'];
        $docPath = $user['documento_validacion'];

        if ($imagen && $imagen['error'] == 0) {
            $imgPath = 'uploads/fundaciones/' . time() . '_' . basename($imagen['name']);
            move_uploaded_file($imagen['tmp_name'], $imgPath);
        }

        if ($documento && $documento['error'] == 0) {
            $docPath = 'uploads/documentos/' . time() . '_' . basename($documento['name']);
            move_uploaded_file($documento['tmp_name'], $docPath);
        }

        $metodo_pago = json_encode([
            'titular' => $titular,
            'tarjeta' => $tarjeta,
            'exp_mes' => $exp_mes,
            'exp_anio' => $exp_anio,
            'cvv' => $cvv
        ]);

        $sql = "UPDATE usuarios 
                SET nombre = ?, email = ?, contacto = ?, direccion = ?, imagen = ?, documento_validacion = ?, metodo_pago = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$nombre, $email, $contacto, $direccion, $imgPath, $docPath, $metodo_pago, $user_id]);
            $_SESSION['nombre'] = $nombre;
            $success = "Datos actualizados correctamente.";
        } catch(PDOException $e) {
            $errors[] = "Error al actualizar los datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Administrar Mi Fundación - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body { background-color: #f4f6fa; color: #2c2c2c; }
    .container {
      margin-top: 50px;
      max-width: 700px;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }
    h2 { color: #2b2d42; text-align: center; margin-bottom: 25px; font-weight: 700; }
    .btn-primary { background-color: #6c63ff; border-color: #6c63ff; }
    .btn-primary:hover { background-color: #574fd6; }
    .btn-secondary { background-color: #adb5bd; }
    .alert-danger { background-color: #ffe3e3; color: #c92a2a; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; }

    .modal-custom {
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      display: none; justify-content: center; align-items: center;
      z-index: 1050;
    }
    .modal-content-custom {
      background: white; padding: 20px 25px; border-radius: 8px;
      width: 100%; max-width: 500px;
    }
  </style>
  <script>
    function abrirModal() {
      document.getElementById('modalPago').style.display = 'flex';
    }
    function cerrarModal() {
      document.getElementById('modalPago').style.display = 'none';
    }
    function guardarModal() {
      document.getElementById('titular').value = document.getElementById('modal_titular').value;
      document.getElementById('tarjeta').value = document.getElementById('modal_tarjeta').value;
      document.getElementById('exp_mes').value = document.getElementById('modal_exp_mes').value;
      document.getElementById('exp_anio').value = document.getElementById('modal_exp_anio').value;
      document.getElementById('cvv').value = document.getElementById('modal_cvv').value;
      cerrarModal();
    }
  </script>
</head>
<body>

<div class="container">
  <h2>Administrar Mi Fundación</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Nombre de la Fundación:</label>
      <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
    </div>
    <div class="form-group">
      <label>Correo electrónico:</label>
      <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <div class="form-group">
      <label>Contacto:</label>
      <input type="text" class="form-control" name="contacto" value="<?= htmlspecialchars($user['contacto']) ?>">
    </div>
    <div class="form-group">
      <label>Dirección:</label>
      <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($user['direccion']) ?>">
    </div>
    <div class="form-group">
      <label>Imagen de la Fundación:</label>
      <input type="file" class="form-control-file" name="imagen" accept="image/*">
    </div>
    <div class="form-group">
      <label>Documento de Validación (PDF/JPG):</label>
      <input type="file" class="form-control-file" name="documento" accept=".pdf,.jpg,.jpeg,.png">
    </div>

    <input type="hidden" name="titular" id="titular">
    <input type="hidden" name="tarjeta" id="tarjeta">
    <input type="hidden" name="exp_mes" id="exp_mes">
    <input type="hidden" name="exp_anio" id="exp_anio">
    <input type="hidden" name="cvv" id="cvv">

    <button type="button" class="btn btn-secondary btn-block" onclick="abrirModal()">Editar Método de Pago</button>
    <button type="submit" class="btn btn-primary btn-block mt-3">Actualizar Información</button>
  </form>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
  </div>
</div>

<!-- Modal método de pago -->
<div class="modal-custom" id="modalPago">
  <div class="modal-content-custom">
    <h5 class="text-center mb-3">Método de Pago</h5>
    <div class="form-group">
      <label>Titular:</label>
      <input type="text" class="form-control" id="modal_titular">
    </div>
    <div class="form-group">
      <label>Número de tarjeta:</label>
      <input type="text" class="form-control" id="modal_tarjeta" maxlength="16">
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Mes:</label>
        <input type="text" class="form-control" id="modal_exp_mes" maxlength="2" placeholder="MM">
      </div>
      <div class="form-group col-md-6">
        <label>Año:</label>
        <input type="text" class="form-control" id="modal_exp_anio" maxlength="2" placeholder="YY">
      </div>
    </div>
    <div class="form-group">
      <label>CVV:</label>
      <input type="text" class="form-control" id="modal_cvv" maxlength="4">
    </div>
    <div class="text-right">
      <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button type="button" class="btn btn-primary" onclick="guardarModal()">Guardar</button>
    </div>
  </div>
</div>
</body>
</html>
