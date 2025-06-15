<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$claseMensaje = "";

// Insertar cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && $_POST["accion"] === "agregar") {
    $nom1 = trim($_POST["nom1"]);
    $nom2 = trim($_POST["nom2"]);
    $apell1 = trim($_POST["apell1"]);
    $apell2 = trim($_POST["apell2"]);
    $direc = trim($_POST["direc"]);
    $movil = trim($_POST["movil"]);
    $correo = trim($_POST["correo"]);

    if (!empty($nom1) && !empty($apell1) && !empty($movil)) {
        $stmt = $conn->prepare("INSERT INTO cliente (nom1, nom2, apell1, apell2, direc, movil, correo, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')");
        $stmt->bind_param("sssssss", $nom1, $nom2, $apell1, $apell2, $direc, $movil, $correo);
        if ($stmt->execute()) {
            $mensaje = "✅ Cliente registrado correctamente.";
            $claseMensaje = "alert-success";
        } else {
            $mensaje = "❌ Error al registrar cliente.";
            $claseMensaje = "alert-danger";
        }
    } else {
        $mensaje = "❌ Nombre, apellido y móvil son obligatorios.";
        $claseMensaje = "alert-warning";
    }
}

// Obtener todos los clientes
$clientes = $conn->query("SELECT * FROM cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Clientes - AgroConecta</title>
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
        <li class="nav-item"><a class="nav-link active" href="clientes.php">Gestión Clientes</a></li>
      </ul>
      <span class="navbar-text me-3">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?></span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-5 p-4">
    <h2 class="mb-4 text-center text-primary fw-bold">Gestión de Clientes</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= $claseMensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-person-plus-fill"></i> Nuevo Cliente
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nombre completo</th>
                    <th>Dirección</th>
                    <th>Móvil</th>
                    <th>Correo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($clientes && $clientes->num_rows > 0): ?>
                    <?php while ($fila = $clientes->fetch_assoc()): ?>
                        <tr class="<?= $fila['estado'] === 'activo' ? '' : 'table-danger' ?>">
                            <td><?= $fila["idc"] ?></td>
                            <td><?= htmlspecialchars($fila["nom1"] . " " . $fila["nom2"] . " " . $fila["apell1"] . " " . $fila["apell2"]) ?></td>
                            <td><?= htmlspecialchars($fila["direc"]) ?></td>
                            <td><?= htmlspecialchars($fila["movil"]) ?></td>
                            <td><?= htmlspecialchars($fila["correo"]) ?></td>
                            <td><span class="badge <?= $fila['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst($fila['estado']) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay clientes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-outline-primary btn-lg">← Salir al Inicio</a>
    </div>
</div>

<!-- Modal Agregar Cliente -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="clientes.php">
        <input type="hidden" name="accion" value="agregar">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label text-dark">Primer nombre</label>
                    <input type="text" name="nom1" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Segundo nombre</label>
                    <input type="text" name="nom2" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Primer apellido</label>
                    <input type="text" name="apell1" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Segundo apellido</label>
                    <input type="text" name="apell2" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Dirección</label>
                    <input type="text" name="direc" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Móvil</label>
                    <input type="text" name="movil" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-dark">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control">
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
