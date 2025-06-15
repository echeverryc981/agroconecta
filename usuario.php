<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$claseMensaje = "";

// Agregar o editar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    $nombreu = trim($_POST["nombreu"]);
    $contrasena = trim($_POST["contrasena"]);
    $idperfil = intval($_POST["idperfil"]);
    $idpersona = intval($_POST["idpersona"]);

    if ($accion === "agregar") {
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE nombreu = ?");
        $stmt->bind_param("s", $nombreu);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $mensaje = "❌ El nombre de usuario ya existe.";
            $claseMensaje = "alert-danger";
        } else {
            $stmt = $conn->prepare("INSERT INTO Usuario (idusuario, nombreu, contrasena, idperfil, idpersona, estado) VALUES (?, ?, ?, ?, ?, 'activo')");
            $stmt->bind_param("issii", $idpersona, $nombreu, $contrasena, $idperfil, $idpersona);
            if ($stmt->execute()) {
                $mensaje = "✅ Usuario agregado correctamente.";
                $claseMensaje = "alert-success";
            } else {
                $mensaje = "❌ Error al agregar usuario.";
                $claseMensaje = "alert-danger";
            }
        }
    } elseif ($accion === "editar") {
        $idusuario = intval($_POST["idusuario"]);
        $stmt = $conn->prepare("UPDATE Usuario SET nombreu = ?, contrasena = ?, idperfil = ?, idpersona = ? WHERE idusuario = ?");
        $stmt->bind_param("ssiii", $nombreu, $contrasena, $idperfil, $idpersona, $idusuario);
        if ($stmt->execute()) {
            $mensaje = "✅ Usuario actualizado correctamente.";
            $claseMensaje = "alert-success";
        } else {
            $mensaje = "❌ Error al actualizar usuario.";
            $claseMensaje = "alert-danger";
        }
    }
}

// Cambiar estado
if (isset($_GET["toggle_estado"])) {
    $id = intval($_GET["toggle_estado"]);
    $estado_actual = $conn->query("SELECT estado FROM Usuario WHERE idusuario = $id")->fetch_assoc()["estado"];
    $nuevo_estado = ($estado_actual === "activo") ? "inactivo" : "activo";
    $conn->query("UPDATE Usuario SET estado = '$nuevo_estado' WHERE idusuario = $id");
    header("Location: usuario.php");
    exit();
}

// Cargar datos
$usuarios = $conn->query("SELECT u.*, p.descripc AS perfil, CONCAT(pe.nom1, ' ', pe.apell1) AS persona 
                          FROM Usuario u 
                          JOIN Perfil p ON u.idperfil = p.idperfil 
                          JOIN Persona pe ON u.idpersona = pe.idpersona");

//print_r($usuarios);

$perfiles = $conn->query("SELECT * FROM Perfil WHERE estado = 'activo'");
$personas = $conn->query("SELECT * FROM Persona WHERE estado = 'activo'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Usuario - AgroConecta</title>
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
        <li class="nav-item"><a class="nav-link active" href="usuario.php">Gestión Usuario</a></li>
      </ul>
      <span class="navbar-text me-3">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?></span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-5 p-4">
    <h2 class="mb-4 text-center text-primary fw-bold">Gestión de Usuarios</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= $claseMensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Perfil</th>
                    <th>Persona</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                    <?php while ($fila = $usuarios->fetch_assoc()): ?>
                        <tr class="<?= $fila['estado'] === 'activo' ? '' : 'table-danger' ?>">
                            <td><?= $fila["idusuario"] ?></td>
                            <td><?= htmlspecialchars($fila["nombreu"]) ?></td>
                            <td><?= htmlspecialchars($fila["perfil"]) ?></td>
                            <td><?= htmlspecialchars($fila["persona"]) ?></td>
                            <td>
                                <span class="badge <?= $fila['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($fila['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm me-2"
                                        onclick="cargarDatosEditar(
                                            <?= $fila['idusuario'] ?>,
                                            '<?= htmlspecialchars($fila['nombreu'], ENT_QUOTES) ?>',
                                            <?= $fila['idperfil'] ?>,
                                            <?= $fila['idpersona'] ?>
                                        )"
                                        data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                <a href="usuario.php?toggle_estado=<?= $fila['idusuario'] ?>" class="btn btn-sm <?= $fila['estado'] === 'activo' ? 'btn-danger' : 'btn-secondary' ?>" onclick="return confirm('¿Está seguro?')">
                                    <i class="bi <?= $fila['estado'] === 'activo' ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
                                    <?= $fila['estado'] === 'activo' ? 'Inhabilitar' : 'Activar' ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-outline-primary btn-lg">← Salir al Inicio</a>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="usuario.php">
        <input type="hidden" name="accion" value="agregar">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label class="form-label text-dark">Usuario</label>
                <input type="text" name="nombreu" class="form-control" required>
                <label class="form-label mt-2 text-dark">Contraseña</label>
                <input type="password" name="contrasena" class="form-control" required>
                <label class="form-label mt-2 text-dark">Perfil</label>
                <select name="idperfil" class="form-select" required>
                    <?php while ($p = $perfiles->fetch_assoc()): ?>
                        <option value="<?= $p['idperfil'] ?>"><?= $p['descripc'] ?></option>
                    <?php endwhile; ?>
                </select>
                <label class="form-label mt-2 text-dark">Persona</label>
                <select name="idpersona" class="form-select" required>
                    <option value="" disabled selected>-- Seleccione una persona --</option>
                    <?php while ($p = $personas->fetch_assoc()): ?>
                        <option value="<?= $p['idpersona'] ?>"><?= $p['nom1'] . ' ' . $p['apell1'] ?></option>
                    <?php endwhile; ?>
                </select>

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
    <form method="POST" action="usuario.php">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="idusuario" id="editarId">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label class="form-label text-dark">Usuario</label>
                <input type="text" name="nombreu" id="editarNombreu" class="form-control" required>
                <label class="form-label mt-2 text-dark">Contraseña</label>
                <input type="password" name="contrasena" class="form-control" required>
                <label class="form-label mt-2 text-dark">Perfil</label>
                <select name="idperfil" id="editarPerfil" class="form-select" required>
                    <?php
                    $perfiles->data_seek(0);
                    while ($p = $perfiles->fetch_assoc()):
                    ?>
                        <option value="<?= $p['idperfil'] ?>"><?= $p['descripc'] ?></option>
                    <?php endwhile; ?>
                </select>
                <label class="form-label mt-2 text-dark">Persona</label>
                <select name="idpersona" id="editarPersona" class="form-select" required>
                    <?php
                    $personas->data_seek(0);
                    while ($p = $personas->fetch_assoc()):
                    ?>
                        <option value="<?= $p['idpersona'] ?>"><?= $p['nom1'] . ' ' . $p['apell1'] ?></option>
                    <?php endwhile; ?>
                </select>
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
function cargarDatosEditar(id, nombreu, idperfil, idpersona) {
    document.getElementById("editarId").value = id;
    document.getElementById("editarNombreu").value = nombreu;
    document.getElementById("editarPerfil").value = idperfil;
    document.getElementById("editarPersona").value = idpersona;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
