<?php
require_once 'db.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email           = trim($_POST['email']);
    $new_password    = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($email)) {
        $errors[] = "El correo es requerido.";
    }
    if (empty($new_password)) {
        $errors[] = "La nueva contraseña es requerida.";
    }
    if ($new_password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    
    if (empty($errors)) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $errors[] = "No se encontró un usuario con ese correo.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET password = ? WHERE email = ?";
            $stmt_update = $pdo->prepare($sql_update);
            if ($stmt_update->execute([$hashed_password, $email])) {
                $success = "Contraseña actualizada exitosamente. Ahora puedes iniciar sesión.";
            } else {
                $errors[] = "Error al actualizar la contraseña.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Contraseña - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f6fa;
      color: #2c2c2c;
    }

    .container {
      margin-top: 60px;
      margin-bottom: 60px;
      max-width: 500px;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      color: #2b2d42;
      font-weight: 700;
      margin-bottom: 25px;
    }

    label {
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

    .alert-danger {
      background-color: #ffe3e3;
      border-color: #ff6b6b;
      color: #c92a2a;
    }

    .alert-success {
      background-color: #d1e7dd;
      border-color: #badbcc;
      color: #0f5132;
    }

    a {
      color: #6c63ff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
      color: #574fd6;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Recuperar Contraseña</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <form action="recover_password.php" method="POST">
     <div class="form-group">
         <label for="email">Correo Electrónico:</label>
         <input type="email" class="form-control" id="email" name="email" required>
     </div>
     <div class="form-group">
         <label for="new_password">Nueva Contraseña:</label>
         <input type="password" class="form-control" id="new_password" name="new_password" required>
     </div>
     <div class="form-group">
         <label for="confirm_password">Confirmar Contraseña:</label>
         <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
     </div>
     <button type="submit" class="btn btn-primary btn-block">Actualizar Contraseña</button>
  </form>

  <p class="mt-4 text-center"><a href="login.php">← Volver a Iniciar Sesión</a></p>
</div>
</body>
</html>
