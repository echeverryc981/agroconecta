<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'agroconecta';
$user = 'root';
$pass = ''; // Ajusta si tienes contraseña

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    mostrarMensaje("Conexión fallida: " . $conn->connect_error, "danger");
    exit;
}

function mostrarMensaje($mensaje, $tipo = "success") {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Registro | AgroConecta</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light d-flex justify-content-center align-items-center' style='height: 100vh;'>
        <div class='container'>
            <div class='alert alert-$tipo text-center shadow-lg'>
                <h4 class='mb-3'>" . ($tipo === 'success' ? '✅ Éxito' : '❌ Error') . "</h4>
                <p>$mensaje</p>
                <a href='register.php' class='btn btn-outline-primary mt-3'>Volver</a>
            </div>
        </div>
    </body>
    </html>
    ";
    exit;
}

// Obtener datos
$doc = trim($_POST['doc']);
$nom1 = trim($_POST['nom1']);
$nom2 = trim($_POST['nom2']);
$apell1 = trim($_POST['apell1']);
$apell2 = trim($_POST['apell2']);
$direccion = trim($_POST['direccion']);
$tele = trim($_POST['tele']);
$movil = trim($_POST['movil']);
$correo = trim($_POST['correo']);
$fecha_nac = $_POST['fecha_nac'];
$nombreu = trim($_POST['nombreu']);
$contrasena = $_POST['contrasena'];
$idperfil = $_POST['idperfil'];
$estado = 'activo';

// Validaciones
if (preg_match('/\s/', $nombreu)) {
    mostrarMensaje("El nombre de usuario no puede contener espacios.", "warning");
}
if (!preg_match('/[A-Z]/', $contrasena) ||
    !preg_match('/[0-9]/', $contrasena) ||
    !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $contrasena)) {
    mostrarMensaje("La contraseña debe tener al menos una mayúscula, un número y un carácter especial.", "warning");
}

// Validar si ya existe ese usuario
$stmt_check = $conn->prepare("SELECT * FROM usuario WHERE nombreu = ?");
$stmt_check->bind_param("s", $nombreu);
$stmt_check->execute();
$result = $stmt_check->get_result();
if ($result->num_rows > 0) {
    mostrarMensaje("El nombre de usuario ya está registrado.", "danger");
}
$stmt_check->close();

// Insertar persona
$stmt_persona = $conn->prepare("INSERT INTO persona (idpersona, nom1, nom2, apell1, apell2, direccion, tele, movil, correo, fecha_nac, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt_persona->bind_param("sssssssssss", $doc, $nom1, $nom2, $apell1, $apell2, $direccion, $tele, $movil, $correo, $fecha_nac, $estado);

if ($stmt_persona->execute()) {
    $idpersona = $stmt_persona->insert_id;

    // Insertar usuario
    $stmt_usuario = $conn->prepare("INSERT INTO usuario (idusuario, nombreu, contrasena, idperfil, idpersona, estado) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_usuario->bind_param("sssiss", $doc, $nombreu, $contrasena, $idperfil, $idpersona, $estado);

    if ($stmt_usuario->execute()) {
        mostrarMensaje("Usuario registrado exitosamente.");
    } else {
        mostrarMensaje("Error al registrar el usuario: " . $stmt_usuario->error, "danger");
    }

    $stmt_usuario->close();
} else {
    mostrarMensaje("Error al registrar la persona: " . $stmt_persona->error, "danger");
}

$stmt_persona->close();
$conn->close();
?>
