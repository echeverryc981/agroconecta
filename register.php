<?php
require 'db.php';

// Obtener perfiles disponibles
$perfiles = $conn->query("SELECT idperfil, descripc FROM Perfil WHERE estado = 'activo'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - AgroConecta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
        body {
            background: url('img/agroconecta.png') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            z-index: 10;
        }

        .card {
            background: rgba(255, 255, 255, 0.8); /* Fondo blanco semi-transparente */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Sombra suave */
            backdrop-filter: blur(10px); /* Difumina el fondo */
        }

        .card h3 {
            font-weight: bold;
            color: #007bff;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.7); /* Campos de formulario con opacidad */
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 1);
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.7);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .text-decoration-none {
            color: #007bff;
        }

        .text-decoration-none:hover {
            color: #0056b3;
        }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow p-4">
        <h3 class="text-center mb-4">Registro en AgroConecta</h3>
        <form action="save_user.php" method="POST">
          <h5 class="mb-3">Datos personales</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Documento Identificación</label>
              <input type="number" name="doc" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Primer nombre</label>
              <input type="text" name="nom1" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Segundo nombre</label>
              <input type="text" name="nom2" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Primer apellido</label>
              <input type="text" name="apell1" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Segundo apellido</label>
              <input type="text" name="apell2" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Teléfono</label>
              <input type="text" name="tele" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Móvil</label>
              <input type="text" name="movil" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Fecha de nacimiento</label>
              <input type="date" name="fecha_nac" class="form-control">
            </div>
          </div>

          <h5 class="mt-4 mb-3">Datos de usuario</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de usuario</label>
              <input type="text" name="nombreu" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" name="contrasena" class="form-control" required>
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Perfil</label>
              <select name="idperfil" class="form-select" required>
                <option value="">Seleccione un perfil</option>
                <?php while ($perfil = $perfiles->fetch_assoc()): ?>
                  <option value="<?= $perfil['idperfil'] ?>"><?= htmlspecialchars($perfil['descripc']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary px-5">Registrarse</button>
          </div>
        </form>
        <div class="mt-3 text-center">
          <a href="index.php" class="text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelector('input[name="nombreu"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/\s/g, ''); // elimina todos los espacios
  });
</script>
<script>
  // Eliminar espacios en tiempo real en el nombre de usuario
  const inputNombreu = document.querySelector('input[name="nombreu"]');
  inputNombreu.addEventListener('input', function() {
    this.value = this.value.replace(/\s/g, '');
  });

  // Validar contraseña al enviar formulario
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    const password = form.querySelector('input[name="contrasena"]').value;

    // Expresiones regulares para validar contraseña
    const hasNumber = /\d/;
    const hasUppercase = /[A-Z]/;
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;

    let errors = [];

    if (!hasNumber.test(password)) {
      errors.push("La contraseña debe contener al menos un número.");
    }
    if (!hasUppercase.test(password)) {
      errors.push("La contraseña debe contener al menos una letra mayúscula.");
    }
    if (!hasSpecialChar.test(password)) {
      errors.push("La contraseña debe contener al menos un carácter especial.");
    }

    if (errors.length > 0) {
      e.preventDefault(); // Evita enviar el formulario
      alert(errors.join("\n"));
    }
  });
</script>

</body>
</html>
