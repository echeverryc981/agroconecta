<?php
session_start();
require 'db.php';

$nombreu = $_POST['nombreu'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT u.idusuario, u.contrasena, p.nom1 FROM Usuario u
        JOIN Persona p ON u.idpersona = p.idpersona
        WHERE u.nombreu = ? AND u.estado = 'activo'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombreu);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($contrasena === $row['contrasena']) {
        $_SESSION['usuario'] = $row['nom1'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Contraseña incorrecta.";
    }
} else {
    $_SESSION['error'] = "Usuario no registrado.";
}

header("Location: index.php");
