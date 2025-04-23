<?php
require_once 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre           = trim($_POST['nombre']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contacto         = trim($_POST['contacto']);
    $direccion        = trim($_POST['direccion']);
    $imagen           = $_FILES['imagen'] ?? null;
    $documento        = $_FILES['documento'] ?? null;

    // Método de pago estructurado
    $titular     = trim($_POST['titular']);
    $tarjeta     = trim($_POST['tarjeta']);
    $exp_mes     = trim($_POST['exp_mes']);
    $exp_anio    = trim($_POST['exp_anio']);
    $cvv         = trim($_POST['cvv']);

    if (empty($nombre)) $errors[] = 'El nombre de la fundación es requerido.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El email es inválido.';
    if (empty($password)) $errors[] = 'La contraseña es requerida.';
    if ($password !== $confirm_password) $errors[] = 'Las contraseñas no coinciden.';
    if (empty($contacto)) $errors[] = 'El contacto es requerido.';
    if (empty($direccion)) $errors[] = 'La dirección es requerida.';
    if (!$imagen || $imagen['error'] !== 0) $errors[] = 'Debes subir una imagen de tu fundación.';
    if (!$documento || $documento['error'] !== 0) $errors[] = 'Debes subir un documento de validación.';

    if (empty($titular) || empty($tarjeta) || empty($exp_mes) || empty($exp_anio) || empty($cvv)) {
        $errors[] = 'Todos los campos del método de pago son obligatorios.';
    }

    if (empty($errors)) {
        $imgFolder = 'uploads/fundaciones/';
        $docFolder = 'uploads/documentos/';
        
        if (!is_dir($imgFolder)) {
            mkdir($imgFolder, 0777, true);
        }
        if (!is_dir($docFolder)) {
            mkdir($docFolder, 0777, true);
        }
        
        $imgPath = $imgFolder . 'img_' . time() . '_' . basename($imagen['name']);
        $docPath = $docFolder . 'doc_' . time() . '_' . basename($documento['name']);
        
        if (!move_uploaded_file($imagen['tmp_name'], $imgPath)) {
            $errors[] = "No se pudo guardar la imagen de la fundación.";
        }
        
        if (!move_uploaded_file($documento['tmp_name'], $docPath)) {
            $errors[] = "No se pudo guardar el documento de validación.";
        }
        

        $metodo_pago = json_encode([
            'titular'   => $titular,
            'tarjeta'   => $tarjeta,
            'exp_mes'   => $exp_mes,
            'exp_anio'  => $exp_anio,
            'cvv'       => $cvv
        ]);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, tipo_usuario, contacto, direccion, imagen, documento_validacion, metodo_pago) 
                VALUES (?, ?, ?, 'ong', ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$nombre, $email, $hashed_password, $contacto, $direccion, $imgPath, $docPath, $metodo_pago]);
            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            $errors[] = $e->getCode() == 23000 ? "El email ya está registrado." : "Error en el registro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Fundación - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f6fa;
    }
    .container {
      max-width: 650px;
      background-color: #fff;
      padding: 30px;
      margin-top: 60px;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.05);
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
      color: #c92a2a;
    }
    .modal-custom {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1050;
    }
    .modal-content-custom {
      background: white;
      padding: 20px 25px;
      border-radius: 8px;
      width: 100%;
      max-width: 500px;
    }
  </style>
  <script>
    function abrirModal() {
      document.getElementById('modalPago').style.display = 'flex';
    }

    function cerrarModal() {
      document.getElementById('modalPago').style.display = 'none';
    }

    function guardarMetodoPago() {
      cerrarModal();
    }
  </script>
</head>
<body>
<div class="container">
  <h2 class="text-center">Registro de Fundación</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger mt-4">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="mt-4">
    <div class="form-group">
      <label for="nombre">Nombre de la Fundación:</label>
      <input type="text" class="form-control" name="nombre" required>
    </div>
    <div class="form-group">
      <label for="email">Correo electrónico:</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Contraseña:</label>
      <input type="password" class="form-control" name="password" required>
    </div>
    <div class="form-group">
      <label for="confirm_password">Confirmar Contraseña:</label>
      <input type="password" class="form-control" name="confirm_password" required>
    </div>
    <div class="form-group">
      <label for="contacto">Contacto:</label>
      <input type="text" class="form-control" name="contacto" required>
    </div>
    <div class="form-group">
      <label for="direccion">Dirección:</label>
      <input type="text" class="form-control" name="direccion" required>
    </div>
    <div class="form-group">
      <label for="imagen">Imagen de la Fundación:</label>
      <input type="file" class="form-control-file" name="imagen" accept="image/*" required>
    </div>
    <div class="form-group">
      <label for="documento">Documento de Validación:</label>
      <input type="file" class="form-control-file" name="documento" accept=".pdf,.jpg,.jpeg,.png" required>
    </div>

    <!-- Inputs ocultos para que no marquen error de formulario -->
    <input type="hidden" name="titular" id="titular">
    <input type="hidden" name="tarjeta" id="tarjeta">
    <input type="hidden" name="exp_mes" id="exp_mes">
    <input type="hidden" name="exp_anio" id="exp_anio">
    <input type="hidden" name="cvv" id="cvv">

    <button type="button" class="btn btn-secondary btn-block" onclick="abrirModal()">Añadir Método de Pago</button>
    <button type="submit" class="btn btn-primary btn-block mt-3">Registrar Fundación</button>
  </form>
  <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<!-- Modal de método de pago -->
<div class="modal-custom" id="modalPago">
  <div class="modal-content-custom">
    <h5 class="text-center mb-3">Método de Pago</h5>
    <div class="form-group">
      <label>Titular:</label>
      <input type="text" class="form-control" id="modal_titular" required>
    </div>
    <div class="form-group">
      <label>Número de tarjeta:</label>
      <input type="text" class="form-control" id="modal_tarjeta" maxlength="16" required>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Mes:</label>
        <input type="text" class="form-control" id="modal_exp_mes" maxlength="2" placeholder="MM" required>
      </div>
      <div class="form-group col-md-6">
        <label>Año:</label>
        <input type="text" class="form-control" id="modal_exp_anio" maxlength="2" placeholder="YY" required>
      </div>
    </div>
    <div class="form-group">
      <label>CVV:</label>
      <input type="text" class="form-control" id="modal_cvv" maxlength="4" required>
    </div>
    <div class="text-right">
      <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button type="button" class="btn btn-primary" onclick="guardarModal()">Guardar</button>
    </div>
  </div>
</div>

<script>
  function guardarModal() {
    document.getElementById('titular').value = document.getElementById('modal_titular').value;
    document.getElementById('tarjeta').value = document.getElementById('modal_tarjeta').value;
    document.getElementById('exp_mes').value = document.getElementById('modal_exp_mes').value;
    document.getElementById('exp_anio').value = document.getElementById('modal_exp_anio').value;
    document.getElementById('cvv').value = document.getElementById('modal_cvv').value;
    cerrarModal();
  }
</script>
</body>
</html>
