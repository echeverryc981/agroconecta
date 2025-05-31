-- Se crea la base de datos
CREATE DATABASE IF NOT EXISTS agroconecta;
USE agroconecta;

-- Se crea la Tabla Perfil
CREATE TABLE Perfil (
    idperfil INT AUTO_INCREMENT PRIMARY KEY,
    descripc VARCHAR(100) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- Tabla Persona
CREATE TABLE Persona (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    nom1 VARCHAR(50) NOT NULL,
    nom2 VARCHAR(50),
    apell1 VARCHAR(50) NOT NULL,
    apell2 VARCHAR(50),
    direccion VARCHAR(255),
    tele VARCHAR(20),
    movil VARCHAR(20),
    correo VARCHAR(100) UNIQUE,
    fecha_nac DATE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- Se crea la Tabla Usuario
CREATE TABLE Usuario (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    nombreu VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    idperfil INT NOT NULL,
    idpersona INT NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (idperfil) REFERENCES Perfil(idperfil),
    FOREIGN KEY (idpersona) REFERENCES Persona(idpersona),
    UNIQUE (idpersona) -- Una persona solo puede tener un usuario
);
