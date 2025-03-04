<?php
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

// Evitar cambiar el estado de un administrador
if ($user['tipo_usuario'] == 'admin') {
    echo "No se puede cambiar el estado de un administrador.";
    exit;
}

// Cambiar de estado: si está activo (b_logico == 1) se desactiva (0), y viceversa
$newState = ($user['b_logico'] == 1) ? 0 : 1;
$sql_update = "UPDATE usuarios SET b_logico = ? WHERE id = ?";
$stmt_update = $pdo->prepare($sql_update);
if ($stmt_update->execute([$newState, $userId])) {
    header("Location: admin_users.php?toggle=1");
    exit;
} else {
    $errorInfo = $stmt_update->errorInfo();
    echo "Error al cambiar el estado del usuario: " . htmlspecialchars($errorInfo[2]);
}
?>
