<?php
// delete_campaign.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'ong') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_campaigns.php");
    exit;
}

$campaignId = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verificar que la campaña pertenezca a la fundación
$sql = "SELECT * FROM campanas WHERE id = ? AND id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$campaignId, $user_id]);
$campaign = $stmt->fetch();

if (!$campaign) {
    echo "Campaña no encontrada o no tienes permiso para deshabilitarla.";
    exit;
}

// Actualizar el campo b_logico a 0 para marcar la campaña como inactiva (deshabilitada)
$sql_update = "UPDATE campanas SET b_logico = 0 WHERE id = ? AND id_usuario = ?";
$stmt_update = $pdo->prepare($sql_update);
if ($stmt_update->execute([$campaignId, $user_id])) {
    header("Location: manage_campaigns.php?disabled=1");
    exit;
} else {
    $errorInfo = $stmt_update->errorInfo();
    echo "Error al deshabilitar la campaña: " . htmlspecialchars($errorInfo[2]);
}
?>
