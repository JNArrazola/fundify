<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_campaigns.php");
    exit;
}

$campaignId = $_GET['id'];

// Obtener la campaña
$sql = "SELECT * FROM campanas WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$campaignId]);
$campaign = $stmt->fetch();

if (!$campaign) {
    echo "Campaña no encontrada.";
    exit;
}

// Cambiar de estado: si está activa (b_logico == 1) se desactiva (0), y viceversa
$newState = ($campaign['b_logico'] == 1) ? 0 : 1;
$sql_update = "UPDATE campanas SET b_logico = ? WHERE id = ?";
$stmt_update = $pdo->prepare($sql_update);
if ($stmt_update->execute([$newState, $campaignId])) {
    header("Location: admin_campaigns.php?toggle=1");
    exit;
} else {
    $errorInfo = $stmt_update->errorInfo();
    echo "Error al cambiar el estado de la campaña: " . htmlspecialchars($errorInfo[2]);
}
?>
