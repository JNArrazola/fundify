<?php
// delete_user.php

// Activar la visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$userId = $_GET['id'];

// Obtener la información del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

// Evitar eliminar (desactivar) a un administrador (opcional)
if ($user['tipo_usuario'] == 'admin') {
    echo "No se puede eliminar un administrador.";
    exit;
}

// Realizar un "soft delete": actualizar el campo b_logico a 0
$sql_update = "UPDATE usuarios SET b_logico = 0 WHERE id = ?";
$stmt_update = $pdo->prepare($sql_update);
if ($stmt_update->execute([$userId])) {
    header("Location: admin_users.php?user_deleted=1");
    exit;
} else {
    $errorInfo = $stmt_update->errorInfo();
    echo "Error al desactivar el usuario: " . htmlspecialchars($errorInfo[2]);
}
?>
