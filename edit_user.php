<?php
// edit_user.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_panel.php");
    exit;
}

$userId = $_GET['id'];

// Obtener información del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $tipo_usuario = $_POST['tipo_usuario'];

    if (empty($nombre)) {
        $errors[] = "El nombre es requerido.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email es inválido.";
    }
    if (empty($tipo_usuario)) {
        $errors[] = "El tipo de usuario es requerido.";
    }

    if (empty($errors)) {
        $sql_update = "UPDATE usuarios SET nombre = ?, email = ?, tipo_usuario = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        if ($stmt_update->execute([$nombre, $email, $tipo_usuario, $userId])) {
            $success = "Usuario actualizado correctamente.";
            // Se actualiza la información del usuario
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        } else {
            $errors[] = "Error al actualizar el usuario.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Fundify Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="admin_panel.php">Fundify Admin</a>
      <div class="collapse navbar-collapse">
          <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="nav-link" href="admin_panel.php">Panel</a></li>
              <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
          </ul>
      </div>
  </nav>
  <div class="container mt-5">
      <h2>Editar Usuario</h2>
      <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
              <ul>
                  <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>
      <?php if ($success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <form action="edit_user.php?id=<?php echo $userId; ?>" method="POST">
          <div class="form-group">
              <label for="nombre">Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
          </div>
          <div class="form-group">
              <label for="email">Correo electrónico:</label>
              <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <div class="form-group">
              <label for="tipo_usuario">Tipo de Usuario:</label>
              <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                  <option value="donante" <?php echo ($user['tipo_usuario'] == 'donante') ? 'selected' : ''; ?>>Donante</option>
                  <option value="ong" <?php echo ($user['tipo_usuario'] == 'ong') ? 'selected' : ''; ?>>Fundación</option>
                  <option value="admin" <?php echo ($user['tipo_usuario'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
              </select>
          </div>
          <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
      </form>
      <a href="admin_panel.php" class="btn btn-secondary mt-3">Volver al Panel</a>
  </div>
</body>
</html>
