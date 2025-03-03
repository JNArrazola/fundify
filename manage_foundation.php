<?php
// manage_foundation.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre    = trim($_POST['nombre']);
    $email     = trim($_POST['email']);
    $contacto  = trim($_POST['contacto']);
    $direccion = trim($_POST['direccion']);

    if (empty($nombre)) {
       $errors[] = "El nombre es requerido.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $errors[] = "El email es inválido.";
    }

    if (empty($errors)) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, contacto = ?, direccion = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$nombre, $email, $contacto, $direccion, $user_id]);
            $success = "Datos actualizados correctamente.";
            // Actualizar la sesión si el nombre ha cambiado
            $_SESSION['nombre'] = $nombre;
        } catch(PDOException $e) {
            $errors[] = "Error al actualizar los datos: " . $e->getMessage();
        }
    }
}

// Consultar los datos actuales del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Administrar Mi Fundación - Fundify</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="dashboard.php">Fundify</a>
    <div class="collapse navbar-collapse">
       <ul class="navbar-nav ml-auto">
           <li class="nav-item">
             <a class="nav-link" href="dashboard.php">Dashboard</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="logout.php">Cerrar Sesión</a>
           </li>
       </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Administrar Mi Fundación</h2>
    <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
       <ul>
       <?php foreach($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
       <?php endforeach; ?>
       </ul>
    </div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="manage_foundation.php" method="POST">
       <div class="form-group">
         <label for="nombre">Nombre de la Fundación:</label>
         <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
       </div>
       <div class="form-group">
         <label for="email">Correo electrónico:</label>
         <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
       </div>
       <div class="form-group">
         <label for="contacto">Contacto:</label>
         <input type="text" class="form-control" id="contacto" name="contacto" value="<?php echo htmlspecialchars($user['contacto'] ?? ''); ?>">
       </div>
       <div class="form-group">
         <label for="direccion">Dirección:</label>
         <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user['direccion'] ?? ''); ?>">
       </div>
       <button type="submit" class="btn btn-primary">Actualizar Información</button>
    </form>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>
</body>
</html>
