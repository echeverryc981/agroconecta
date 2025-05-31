<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // No está logueado, redirigir a login
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - AgroConecta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">AgroConecta</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModules" aria-controls="navbarModules" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarModules">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Módulo 1</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Módulo 2</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Configuración</a></li>
      </ul>
      <span class="navbar-text text-white me-3">
        Bienvenido, <?= htmlspecialchars($usuario) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h1>Panel principal</h1>
    <p>Aquí va el contenido del dashboard y los módulos disponibles.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
