<?php
require 'db.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];

    $sql = "SELECT u.nombreu, u.contrasena, p.nom1 FROM Usuario u
            JOIN Persona p ON u.idpersona = p.idpersona
            WHERE p.correo = ? AND u.estado = 'activo'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $usuario = $row['nombreu'];
        $contrasena = $row['contrasena'];
        $nombre = $row['nom1'];

        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP para Hotmail
            

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'echeverryc981@gmail.com'; // Cambia esto
            $mail->Password = 'eomckqvmuvbfoewf';         // Cambia esto
            $mail->SMTPSecure = 'TLS';
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8'; // ✅ Define el charset del correo
            $mail->Encoding = 'base64'; // ✅ (Opcional pero recomendado para UTF-8)

            $mail->setFrom('echeverryc981@gmail.com', 'AgroConecta');
            $mail->addAddress($correo, $nombre);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de acceso - AgroConecta';
            $mail->Body = "
                <h3>Hola $nombre,</h3>
                <p>Tu nombre de usuario es: <strong>$usuario</strong><br>
                Tu contraseña es: <strong>$contrasena</strong></p>
                <p>Equipo de AgroConecta</p>
            ";
            $mail->AltBody = "Hola $nombre,\n\nTu usuario es: $usuario\nTu contraseña es: $contrasena\n\nAgroConecta";

            $mail->send();

            $_SESSION['mensaje'] = "✅ Su usuario y contraseña fue enviado al correo registrado.";
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error al enviar el correo: " . $mail->ErrorInfo;
        }

    } else {
        $_SESSION['error'] = "❌ Correo no registrado en el sistema.";
    }

    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Usuario/Contraseña</title>
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
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .card h3 {
            font-weight: bold;
            color: #007bff;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.7);
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
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h3 class="text-center mb-3">Recuperación de acceso</h3>

                <?php
                if (isset($_SESSION['mensaje'])) {
                    echo "<div class='alert alert-success'>" . $_SESSION['mensaje'] . "</div>";
                    unset($_SESSION['mensaje']);
                }
                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                    unset($_SESSION['error']);
                }
                ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo registrado</label>
                        <input type="email" class="form-control" name="correo" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enviar acceso</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="index.php" class="text-decoration-none">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
