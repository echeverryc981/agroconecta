<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$idperfil = $_SESSION['idperfil'] ?? null;

// Control de acceso por perfil
$mostrarPerfil  = ($idperfil == 1);
$mostrarUsuario = ($idperfil == 1);
$mostrarPersona = in_array($idperfil, [1, 2]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - AgroConecta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('img/agroconecta.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background: rgba(0, 123, 255, 0.9);
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background-color: #00b894;
            border-color: #00b894;
        }
        .btn-primary:hover {
            background-color: #55efc4;
            border-color: #55efc4;
        }
        .card-icon {
            font-size: 50px;
            color: #6c5ce7;
            margin-bottom: 15px;
        }
        .text-center-custom {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">AgroConecta</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModules" aria-controls="navbarModules" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarModules">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
      </ul>
      <span class="navbar-text text-white me-3">
        Bienvenido, <?= htmlspecialchars($usuario) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h1 class="text-center-custom">Panel Principal</h1>
    <p class="text-center-custom">A continuación, puedes acceder a los módulos disponibles:</p>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($mostrarPerfil): ?>
        <!-- Módulo Gestión Perfil -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle card-icon"></i>
                    <h5 class="card-title">Gestión Perfil</h5>
                    <p class="card-text">Crea y edita perfiles de usuario.</p>
                    <a href="perfil.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostrarUsuario): ?>
        <!-- Módulo Gestión Usuario -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill card-icon"></i>
                    <h5 class="card-title">Gestión Usuario</h5>
                    <p class="card-text">Actualiza usuarios y contraseñas.</p>
                    <a href="usuario.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostrarPersona): ?>
        <!-- Módulo Gestión Persona -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-bounding-box card-icon"></i>
                    <h5 class="card-title">Gestión Persona</h5>
                    <p class="card-text">Crea y actualiza datos personales.</p>
                    <a href="persona.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>
        
        <!-- Módulo Gestión de Productos -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam card-icon"></i>
                    <h5 class="card-title">Gestión de Productos</h5>
                    <p class="card-text">Administra productos disponibles.</p>
                    <a href="productos.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Módulo Gestión de Pedidos -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-truck card-icon"></i>
                    <h5 class="card-title">Gestión de Pedidos</h5>
                    <p class="card-text">Consulta y gestiona pedidos realizados.</p>
                    <a href="pedidos.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Módulo Gestión de Clientes -->
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people card-icon"></i>
                    <h5 class="card-title">Gestión de Clientes</h5>
                    <p class="card-text">Administra información de clientes.</p>
                    <a href="clientes.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>



        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
