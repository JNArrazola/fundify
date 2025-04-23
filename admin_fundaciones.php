<?php
// admin_fundaciones.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$sql = "SELECT id, nombre, email, contacto, direccion, imagen, documento_validacion, verificada 
        FROM usuarios WHERE tipo_usuario = 'ong'";
$stmt = $pdo->query($sql);
$fundaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Fundaciones - Fundify Admin</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .table td, .table th {
      vertical-align: middle;
    }
    .img-thumbnail {
      max-width: 100px;
    }
    .doc-link {
      display: inline-block;
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="admin_panel.php">Fundify Admin</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4 text-center">Gestión de Fundaciones</h2>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="alert alert-success text-center">
    Fundación aprobada correctamente.
  </div>
<?php elseif (isset($_GET['error'])): ?>
  <div class="alert alert-danger text-center">
    <?php
      switch ($_GET['error']) {
        case '1':
          echo "Error: solicitud no válida.";
          break;
        case 'notfound':
          echo "Error: Fundación no encontrada o no válida.";
          break;
        case 'sql':
          echo "Error al actualizar la base de datos.";
          break;
        default:
          echo "Ocurrió un error desconocido.";
      }
    ?>
  </div>
<?php endif; ?>


  <?php if (empty($fundaciones)): ?>
    <div class="alert alert-warning text-center">No hay fundaciones registradas aún.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Contacto</th>
            <th>Dirección</th>
            <th>Imagen</th>
            <th>Documento</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fundaciones as $f): ?>
            <tr>
              <td><?= htmlspecialchars($f['nombre']) ?></td>
              <td><?= htmlspecialchars($f['email']) ?></td>
              <td><?= htmlspecialchars($f['contacto']) ?></td>
              <td><?= htmlspecialchars($f['direccion']) ?></td>
              <td>
                <?php if (!empty($f['imagen'])): ?>
                  <img src="<?= htmlspecialchars($f['imagen']) ?>" class="img-thumbnail" alt="Logo Fundación">
                <?php else: ?>
                  <span class="text-muted">Sin imagen</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($f['documento_validacion'])): ?>
                  <a href="<?= htmlspecialchars($f['documento_validacion']) ?>" class="doc-link" target="_blank">Ver documento</a>
                <?php else: ?>
                  <span class="text-muted">No subido</span>
                <?php endif; ?>
              </td>
              <td>
                <?= $f['verificada'] ? '<span class="text-success">Verificada</span>' : '<span class="text-warning">Pendiente</span>' ?>
              </td>
              <td>
                <?php if (!$f['verificada']): ?>
                  <a href="aprobar_fundacion.php?id=<?= $f['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Aprobar esta fundación?')">Aprobar</a>
                <?php else: ?>
                  <span class="text-muted">Sin acciones</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="admin_panel.php" class="btn btn-secondary">← Volver al Panel</a>
  </div>
</div>
</body>
</html>
