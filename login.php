<?php
// login.php
require_once 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) {
        $errors[] = 'El email es requerido.';
    }
    if (empty($password)) {
        $errors[] = 'La contraseña es requerida.';
    }
    
    if (empty($errors)) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            session_regenerate_id(true);
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nombre']       = $user['nombre'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Credenciales incorrectas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Fundify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Iniciar Sesión</h2>
    <?php if(isset($_GET['registered'])): ?>
    <div class="alert alert-success">
        Registro exitoso. Ahora puedes iniciar sesión.
    </div>
    <?php endif; ?>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
         <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" required>
         </div>
         <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
         </div>
         <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    <p class="mt-3">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
</div>
</body>
</html>
