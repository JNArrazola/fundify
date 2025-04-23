<?php
// login.php
require_once 'db.php';

$errors = [];
$email = ""; // variable para conservar el email

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
            if ($user['b_logico'] != 1) {
                $errors[] = "Cuenta desactivada. Por favor, contacta al administrador.";
            } else {
                session_start();
                session_regenerate_id(true);
                $_SESSION['user_id']      = $user['id'];
                $_SESSION['nombre']       = $user['nombre'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
                header("Location: dashboard.php");
                exit;
            }
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
    <style>
        body {
            background-color: #f4f6fa;
            color: #2c2c2c;
        }

        h2 {
            color: #2b2d42;
        }

        .container {
            max-width: 500px;
            margin-top: 60px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background-color: #6c63ff;
            border-color: #6c63ff;
        }

        .btn-primary:hover {
            background-color: #574fd6;
            border-color: #574fd6;
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

        label {
            font-weight: 500;
        }

        a {
            color: #6c63ff;
        }

        a:hover {
            color: #574fd6;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Iniciar Sesión</h2>

    <?php if(isset($_GET['registered'])): ?>
    <div class="alert alert-success mt-4">
        Registro exitoso. Ahora puedes iniciar sesión.
    </div>
    <?php endif; ?>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger mt-4">
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="mt-4">
         <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?php echo htmlspecialchars($email); ?>" required>
         </div>
         <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
         </div>
         <button type="submit" class="btn btn-primary btn-block mt-3">Iniciar Sesión</button>
    </form>

    <p class="mt-3 text-center">
      ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
    </p>
    <p class="mt-2 text-center">
      <a href="recover_password.php">¿Olvidaste tu contraseña?</a>
    </p>
</div>
</body>
</html>
