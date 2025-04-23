<?php
// register_fundacion.php
require_once 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre           = trim($_POST['nombre']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contacto         = trim($_POST['contacto']);
    $direccion        = trim($_POST['direccion']);
    
    if (empty($nombre)) {
        $errors[] = 'El nombre de la fundación es requerido.';
    }
    if (empty($email)) {
        $errors[] = 'El email es requerido.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no es válido.';
    }
    if (empty($password)) {
        $errors[] = 'La contraseña es requerida.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Las contraseñas no coinciden.';
    }
    if (empty($contacto)) {
        $errors[] = 'El contacto es requerido.';
    }
    if (empty($direccion)) {
        $errors[] = 'La dirección es requerida.';
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES (?, ?, ?, 'ong')";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$nombre, $email, $hashed_password]);
            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "El email ya está registrado.";
            } else {
                $errors[] = "Error en el registro: " . $e->getMessage();
            }
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
            color: #2c2c2c;
        }

        h2 {
            color: #2b2d42;
        }

        .container {
            max-width: 600px;
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
    <h2 class="text-center">Registro de Fundación</h2>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger mt-4">
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="register_fundacion.php" method="POST" class="mt-4">
        <div class="form-group">
            <label for="nombre">Nombre de la Fundación:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
         <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" required>
         </div>
         <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
         </div>
         <div class="form-group">
            <label for="confirm_password">Confirmar Contraseña:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
         </div>
         <div class="form-group">
            <label for="contacto">Contacto:</label>
            <input type="text" class="form-control" id="contacto" name="contacto" required>
         </div>
         <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
         </div>
         <button type="submit" class="btn btn-primary btn-block mt-3">Registrar Fundación</button>
    </form>
    <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
</div>
</body>
</html>
