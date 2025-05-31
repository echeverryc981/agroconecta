<!DOCTYPE html>
<html lang="es">
<head>
  <title>AgroConecta - Iniciar Sesión</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow p-4">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
