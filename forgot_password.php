<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'db.php';
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
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';             // Servidor SMTP (ejemplo: Gmail)
        $mail->SMTPAuth = true;
        $mail->Username = 'echeverryc981@hotmail.com';       // Tu correo
        $mail->Password = 'hoazdvokdfzvywlp';        // Tu contraseña de aplicación de Gmail
        $mail->SMTPSecure = 'tls';                    // 'ssl' si usas puerto 465
        $mail->Port = 587;                            // 587 para TLS, 465 para SSL

        // Configuración del mensaje
        $mail->setFrom('echeverryc981@hotmail.com', 'AgroConecta');
        $mail->addAddress($correo, $nombre);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de acceso - AgroConecta';
        $mail->Body = "
            <h3>Hola $nombre,</h3>
            <p>Tu nombre de usuario es: <strong>$usuario</strong></p>
            <p>Tu contraseña es: <strong>$contrasena</strong></p>
            <br>
            <p>Por favor no compartas esta información.</p>
            <p>— Equipo AgroConecta</p>
        ";

        $mail->SMTPDebug = 2;          // Nivel de detalle del debug (1 o 2 es bueno para ver el flujo)
        $mail->Debugoutput = 'html';   // Mostrar el debug en formato HTML para que se vea legible en el navegador
        
        $mail->send();
        $_SESSION['mensaje'] = "✅ Su usuario y contraseña fue enviado al correo registrado.";
} catch (Exception $e) {
    $_SESSION['error'] = "❌ Error al enviar correo: {$mail->ErrorInfo}";
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
