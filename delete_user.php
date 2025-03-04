<?php
// delete_user.php
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

// Obtener la información del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

// Evitar eliminar a un administrador (opcional)
if ($user['tipo_usuario'] == 'admin') {
    echo "No se puede eliminar un administrador.";
    exit;
}

$sql_delete = "DELETE FROM usuarios WHERE id = ?";
$stmt_delete = $pdo->prepare($sql_delete);
if ($stmt_delete->execute([$userId])) {
    header("Location: admin_panel.php?user_deleted=1");
    exit;
} else {
    $errorInfo = $stmt_delete->errorInfo();
    echo "Error al eliminar el usuario: " . htmlspecialchars($errorInfo[2]);
}
?>
