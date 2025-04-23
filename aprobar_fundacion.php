<?php
// aprobar_fundacion.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_fundaciones.php?error=1");
    exit;
}

$fundacion_id = (int) $_GET['id'];

// Validar que exista y sea una ONG
$sql = "SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'ong'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fundacion_id]);
$fundacion = $stmt->fetch();

if (!$fundacion) {
    header("Location: admin_fundaciones.php?error=notfound");
    exit;
}

// Aprobar la fundación
$sql = "UPDATE usuarios SET verificada = 1 WHERE id = ?";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$fundacion_id]);
    header("Location: admin_fundaciones.php?success=1");
    exit;
} catch (PDOException $e) {
    header("Location: admin_fundaciones.php?error=sql");
    exit;
}
?>
