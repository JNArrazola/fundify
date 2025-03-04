<?php
// delete_campaign.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_usuario'] != 'ong' && $_SESSION['tipo_usuario'] != 'admin')) {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirigir según el tipo de usuario
    if ($_SESSION['tipo_usuario'] == 'admin') {
        header("Location: admin_panel.php");
    } else {
        header("Location: manage_campaigns.php");
    }
    exit;
}

$campaignId = $_GET['id'];
$user_id    = $_SESSION['user_id'];
$userType   = $_SESSION['tipo_usuario'];

// Verificar que la campaña exista y, si es una fundación, que pertenezca a ella
if ($userType == 'ong') {
    $sql = "SELECT * FROM campanas WHERE id = ? AND id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$campaignId, $user_id]);
} else { // admin puede deshabilitar cualquier campaña
    $sql = "SELECT * FROM campanas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$campaignId]);
}
$campaign = $stmt->fetch();

if (!$campaign) {
    echo "Campaña no encontrada o no tienes permiso para deshabilitarla.";
    exit;
}

// Actualizar el campo b_logico a 0 para marcar la campaña como inactiva (deshabilitada)
if ($userType == 'ong') {
    $sql_update = "UPDATE campanas SET b_logico = 0 WHERE id = ? AND id_usuario = ?";
    $params = [$campaignId, $user_id];
} else { // admin
    $sql_update = "UPDATE campanas SET b_logico = 0 WHERE id = ?";
    $params = [$campaignId];
}

$stmt_update = $pdo->prepare($sql_update);
if ($stmt_update->execute($params)) {
    // Redirigir según el tipo de usuario
    if ($userType == 'admin') {
         header("Location: admin_panel.php?disabled=1");
    } else {
         header("Location: manage_campaigns.php?disabled=1");
    }
    exit;
} else {
    $errorInfo = $stmt_update->errorInfo();
    echo "Error al deshabilitar la campaña: " . htmlspecialchars($errorInfo[2]);
}
?>
