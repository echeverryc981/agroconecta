<!DOCTYPE html>
<html lang="es">
<head>
  <title>AgroConecta - Iniciar Sesión</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos personalizados -->
  <style>
    /* Estilo para el fondo */
    body, html {
      height: 100%;
      margin: 0;
    }

    .container-fluid {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: url('img/agroconecta.png') no-repeat center center fixed;
      background-size: cover;
    }

    .login-form {
      background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco con opacidad */
      border-radius: 8px;
      padding: 30px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .row {
      height: 100%;
    }

    .col-left {
      display: none; /* Ocultar la columna de la izquierda en pantallas pequeñas */
    }

    .col-right {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Responsividad: cuando el tamaño de la pantalla sea pequeño, poner el formulario encima de la imagen */
    @media (max-width: 768px) {
      .col-left {
        display: block;
        width: 100%;
        height: 50%;
      }

      .col-right {
        width: 100%;
        margin-top: 30px;
      }

      .login-form {
        width: 90%;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <!-- Columna de la imagen a la izquierda (solo visible en pantallas grandes) -->
  <div class="col-left col-12 col-md-6">
    <!-- La imagen de fondo ya está configurada en el contenedor -->
  </div>

  <!-- Columna del formulario -->
  <div class="col-right col-12 col-md-6">
    <div class="login-form">
      <h2 class="text-center mb-4">Bienvenido - Software AgroConecta</h2>

      <?php session_start(); if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="mb-3">
          <label for="nombreu" class="form-label">Usuario</label>
          <input type="text" class="form-control" id="nombreu" name="nombreu" required>
        </div>
        <div class="mb-3">
          <label for="contrasena" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Iniciar Sesión</button>
      </form>

      <div class="mt-3 text-center">
        <a href="register.php" class="text-decoration-none">Registrarse</a> | 
        <a href="forgot_password.php" class="text-decoration-none">¿Olvidó su contraseña?</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
