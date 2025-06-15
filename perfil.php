<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$claseMensaje = "";

// Agregar perfil
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
    if ($_POST["accion"] === "agregar") {
        $nombre = trim($_POST["descripc"]);
        if (!empty($nombre)) {
            $stmt = $conn->prepare("SELECT * FROM perfil WHERE descripc = ?");
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado->num_rows > 0) {
                $mensaje = "❌ El perfil ya existe.";
                $claseMensaje = "alert-danger";
            } else {
                $stmt = $conn->prepare("INSERT INTO perfil (descripc, estado) VALUES (?, 'activo')");
                $stmt->bind_param("s", $nombre);
                if ($stmt->execute()) {
                    $mensaje = "✅ Perfil agregado correctamente.";
                    $claseMensaje = "alert-success";
                } else {
                    $mensaje = "❌ Error al agregar el perfil.";
                    $claseMensaje = "alert-danger";
                }
            }
        } else {
            $mensaje = "❌ El nombre del perfil no puede estar vacío.";
            $claseMensaje = "alert-warning";
        }
    }

    // Editar perfil
    if ($_POST["accion"] === "editar" && isset($_POST["idperfil"])) {
        $id = intval($_POST["idperfil"]);
        $nuevoNombre = trim($_POST["descripc"]);
        if (!empty($nuevoNombre)) {
            $stmt = $conn->prepare("UPDATE perfil SET descripc = ? WHERE idperfil = ?");
            $stmt->bind_param("si", $nuevoNombre, $id);
            if ($stmt->execute()) {
                $mensaje = "✅ Perfil actualizado correctamente.";
                $claseMensaje = "alert-success";
            } else {
                $mensaje = "❌ Error al actualizar el perfil.";
                $claseMensaje = "alert-danger";
            }
        } else {
            $mensaje = "❌ El nombre no puede estar vacío.";
            $claseMensaje = "alert-warning";
        }
    }
}

// Inhabilitar
if (isset($_GET["inhabilitar"])) {
    $id = intval($_GET["inhabilitar"]);
    $conn->query("UPDATE perfil SET estado = 'inactivo' WHERE idperfil = $id");
    header("Location: perfil.php");
    exit();
}

// Activar
if (isset($_GET["activar"])) {
    $id = intval($_GET["activar"]);
    $conn->query("UPDATE perfil SET estado = 'activo' WHERE idperfil = $id");
    header("Location: perfil.php");
    exit();
}
 
$perfiles = $conn->query("SELECT * FROM perfil");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Perfil - AgroConecta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/agroconecta.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            color: #000;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .modal-content {
            background-color: #fff;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">AgroConecta</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="perfil.php">Gestión Perfil</a></li>
      </ul>
      <span class="navbar-text me-3">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?></span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-5 p-4">
    <h2 class="mb-4 text-center text-primary fw-bold">Gestión de Perfiles</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= $claseMensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-circle"></i> Nuevo Perfil
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($perfiles && $perfiles->num_rows > 0): ?>
                    <?php while ($fila = $perfiles->fetch_assoc()): ?>
                        <tr class="<?= $fila['estado'] === 'activo' ? '' : 'table-danger' ?>">
                            <td><?= $fila["idperfil"] ?></td>
                            <td><?= htmlspecialchars($fila["descripc"]) ?></td>
                            <td>
                                <span class="badge <?= $fila['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($fila['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm me-2"
                                        onclick="cargarDatosEditar(<?= $fila['idperfil'] ?>, '<?= htmlspecialchars($fila['descripc'], ENT_QUOTES) ?>')"
                                        data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>

                                <?php if ($fila['estado'] === 'activo'): ?>
                                    <a href="perfil.php?inhabilitar=<?= $fila['idperfil'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro que desea inhabilitar este perfil?')">
                                        <i class="bi bi-x-circle"></i> Inhabilitar
                                    </a>
                                <?php else: ?>
                                    <a href="perfil.php?activar=<?= $fila['idperfil'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-check-circle"></i> Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay perfiles registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-outline-primary btn-lg">
            ← Salir al Inicio
        </a>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="perfil.php">
        <input type="hidden" name="accion" value="agregar">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label for="descripc" class="form-label text-dark">Nombre del perfil</label>
                <input type="text" class="form-control" name="descripc" id="descripc" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="perfil.php">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="idperfil" id="editarId">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label for="editarDescripc" class="form-label text-dark">Nombre del perfil</label>
                <input type="text" class="form-control" name="descripc" id="editarDescripc" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
function cargarDatosEditar(id, descripc) {
    document.getElementById('editarId').value = id;
    document.getElementById('editarDescripc').value = descripc;
}



</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
