-- Se crea la base de datos
CREATE DATABASE IF NOT EXISTS agroconecta;
USE agroconecta;

-- Tabla Perfil
CREATE TABLE Perfil (
    idperfil INT AUTO_INCREMENT PRIMARY KEY,
    descripc VARCHAR(100) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- Tabla Persona (idpersona representa documento de identificación único)
CREATE TABLE Persona (
    idpersona INT PRIMARY KEY, -- aquí se usa el documento único (sin auto_increment)
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

-- Tabla Usuario
CREATE TABLE Usuario (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    nombreu VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    idperfil INT NOT NULL,
    idpersona INT NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (idperfil) REFERENCES Perfil(idperfil),
    FOREIGN KEY (idpersona) REFERENCES Persona(idpersona),
    UNIQUE (idpersona)
);

-- Tabla Modulo
CREATE TABLE Modulo (
    idmodulo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    url VARCHAR(100) NOT NULL
);

-- Tabla Permiso
CREATE TABLE Permiso (
    idpermiso INT AUTO_INCREMENT PRIMARY KEY,
    idperfil INT NOT NULL,
    idmodulo INT NOT NULL,
    FOREIGN KEY (idperfil) REFERENCES Perfil(idperfil),
    FOREIGN KEY (idmodulo) REFERENCES Modulo(idmodulo)
);

-- Tabla TipoProducto
CREATE TABLE TipoProducto (
    codtp INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

-- Tabla Producto
CREATE TABLE Producto (
    codp INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    propiedades TEXT,
    cantidad DECIMAL(10,2),
    unidad_medida VARCHAR(50),
    imagen VARCHAR(255),
    codtp INT,
    FOREIGN KEY (codtp) REFERENCES TipoProducto(codtp)
);

-- Tabla Municipio
CREATE TABLE Municipio (
    codmun INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla Productor (idpersona es PK y FK, sin auto_increment)
CREATE TABLE Productor (
    idp INT PRIMARY KEY,
    codmun INT,
    FOREIGN KEY (idp) REFERENCES Persona(idpersona),
    FOREIGN KEY (codmun) REFERENCES Municipio(codmun)
);

-- Tabla Cliente (idpersona es PK y FK, sin auto_increment)
CREATE TABLE Cliente (
    idc INT PRIMARY KEY,
    codmun INT,
    FOREIGN KEY (idc) REFERENCES Persona(idpersona),
    FOREIGN KEY (codmun) REFERENCES Municipio(codmun)
);
