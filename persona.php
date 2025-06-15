<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$claseMensaje = "";

// Agregar persona
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"])) {
    if ($_POST["accion"] === "agregar") {
       // print_r($_POST);
        $sql = "INSERT INTO Persona (idpersona, nom1, nom2, apell1, apell2, direccion, tele, movil, correo, fecha_nac, estado) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $_POST["doc"], $_POST["nom1"], $_POST["nom2"], $_POST["apell1"], $_POST["apell2"], $_POST["direccion"], $_POST["tele"], $_POST["movil"], $_POST["correo"], $_POST["fecha_nac"]);
        if ($stmt->execute()) {
            $mensaje = "✅ Persona agregada correctamente.";
            $claseMensaje = "alert-success";
        } else {
            $mensaje = "❌ Error al agregar la persona: " . $conn->error;
            $claseMensaje = "alert-danger";
        }
    }

    // Editar persona
    if ($_POST["accion"] === "editar") {
        $sql = "UPDATE Persona SET nom1=?, nom2=?, apell1=?, apell2=?, direccion=?, tele=?, movil=?, correo=?, fecha_nac=? 
                WHERE idpersona=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $_POST["nom1"], $_POST["nom2"], $_POST["apell1"], $_POST["apell2"], $_POST["direccion"], $_POST["tele"], $_POST["movil"], $_POST["correo"], $_POST["fecha_nac"], $_POST["idpersona"]);
        if ($stmt->execute()) {
            $mensaje = "✅ Persona actualizada correctamente.";
            $claseMensaje = "alert-success";
        } else {
            $mensaje = "❌ Error al actualizar la persona: " . $conn->error;
            $claseMensaje = "alert-danger";
        }
    }
}

// Cambiar estado
if (isset($_GET["estado"]) && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $nuevoEstado = $_GET["estado"] === "activo" ? "inactivo" : "activo";
    $conn->query("UPDATE Persona SET estado='$nuevoEstado' WHERE idpersona=$id");
    header("Location: persona.php");
    exit();
}

// Obtener todas las personas
$personas = $conn->query("SELECT * FROM Persona");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Persona - AgroConecta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
        <li class="nav-item"><a class="nav-link active" href="persona.php">Gestión Persona</a></li>
      </ul>
      <span class="navbar-text me-3">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?></span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-5 p-4">
    <h2 class="mb-4 text-center text-primary fw-bold">Gestión de Personas</h2>
    
    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= $claseMensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-person-plus"></i> Nueva Persona
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Fecha Nac.</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $personas->fetch_assoc()): ?>
                    <tr class="<?= $fila["estado"] === 'activo' ? '' : 'table-danger' ?>">
                        <td><?= $fila["idpersona"] ?></td>
                        <td><?= $fila["nom1"] . " " . $fila["nom2"] . " " . $fila["apell1"] . " " . $fila["apell2"] ?></td>
                        <td><?= $fila["direccion"] ?></td>
                        <td><?= $fila["tele"] ?></td>
                        <td><?= $fila["movil"] ?></td>
                        <td><?= $fila["correo"] ?></td>
                        <td><?= $fila["fecha_nac"] ?></td>
                        <td><span class="badge bg-<?= $fila["estado"] === 'activo' ? 'success' : 'secondary' ?>"><?= $fila["estado"] ?></span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $fila["idpersona"] ?>">
                                Editar
                            </button>
                            <a href="persona.php?id=<?= $fila["idpersona"] ?>&estado=<?= $fila["estado"] ?>" class="btn btn-<?= $fila["estado"] === 'activo' ? 'danger' : 'secondary' ?> btn-sm" onclick="return confirm('¿Está seguro?')">
                                <?= $fila["estado"] === 'activo' ? 'Inhabilitar' : 'Habilitar' ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-outline-primary btn-lg">
            ← Salir al Inicio
        </a>
    </div>
</div>

<!-- Modales de edición: fuera de la tabla -->
<?php
$personas->data_seek(0); // Reinicia el cursor para recorrer nuevamente
while ($fila = $personas->fetch_assoc()): ?>
    <div class="modal fade" id="modalEditar<?= $fila["idpersona"] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $fila["idpersona"] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="persona.php">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="idpersona" value="<?= $fila["idpersona"] ?>">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="modalEditarLabel<?= $fila["idpersona"] ?>">Editar Persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-dark" for="nom1<?= $fila["idpersona"] ?>">Primer nombre</label>
                            <input type="text" class="form-control" name="nom1" id="nom1<?= $fila["idpersona"] ?>" required value="<?= $fila["nom1"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="nom2<?= $fila["idpersona"] ?>">Segundo nombre</label>
                            <input type="text" class="form-control" name="nom2" id="nom2<?= $fila["idpersona"] ?>" value="<?= $fila["nom2"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="apell1<?= $fila["idpersona"] ?>">Primer apellido</label>
                            <input type="text" class="form-control" name="apell1" id="apell1<?= $fila["idpersona"] ?>" required value="<?= $fila["apell1"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="apell2<?= $fila["idpersona"] ?>">Segundo apellido</label>
                            <input type="text" class="form-control" name="apell2" id="apell2<?= $fila["idpersona"] ?>" value="<?= $fila["apell2"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="direccion<?= $fila["idpersona"] ?>">Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="direccion<?= $fila["idpersona"] ?>" value="<?= $fila["direccion"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="tele<?= $fila["idpersona"] ?>">Teléfono</label>
                            <input type="text" class="form-control" name="tele" id="tele<?= $fila["idpersona"] ?>" value="<?= $fila["tele"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="movil<?= $fila["idpersona"] ?>">Móvil</label>
                            <input type="text" class="form-control" name="movil" id="movil<?= $fila["idpersona"] ?>" value="<?= $fila["movil"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="correo<?= $fila["idpersona"] ?>">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo<?= $fila["idpersona"] ?>" value="<?= $fila["correo"] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="fecha_nac<?= $fila["idpersona"] ?>">Fecha de nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nac" id="fecha_nac<?= $fila["idpersona"] ?>" value="<?= $fila["fecha_nac"] ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endwhile; ?>

<!-- Modal para agregar nueva persona -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="persona.php">
            <input type="hidden" name="accion" value="agregar">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Nueva Persona</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-dark" for="documentoI">Documento Identificación</label>
                        <input type="number" class="form-control" name="doc" id="documentoI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="nom1Agregar">Primer nombre</label>
                        <input type="text" class="form-control" name="nom1" id="nom1Agregar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="nom2Agregar">Segundo nombre</label>
                        <input type="text" class="form-control" name="nom2" id="nom2Agregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="apell1Agregar">Primer apellido</label>
                        <input type="text" class="form-control" name="apell1" id="apell1Agregar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="apell2Agregar">Segundo apellido</label>
                        <input type="text" class="form-control" name="apell2" id="apell2Agregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="direccionAgregar">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="direccionAgregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="teleAgregar">Teléfono</label>
                        <input type="text" class="form-control" name="tele" id="teleAgregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="movilAgregar">Móvil</label>
                        <input type="text" class="form-control" name="movil" id="movilAgregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="correoAgregar">Correo electrónico</label>
                        <input type="email" class="form-control" name="correo" id="correoAgregar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark" for="fecha_nacAgregar">Fecha de nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nac" id="fecha_nacAgregar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
