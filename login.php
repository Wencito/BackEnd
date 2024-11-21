<?php
session_start();

// Configuración de conexión
$servidor = "localhost";
$usuarioBD = "root";
$clave = "";
$baseDeDatos = "registrar";

// Conexión a la base de datos
$enlace = new mysqli($servidor, $usuarioBD, $clave, $baseDeDatos);

// Verificar conexión
if ($enlace->connect_error) {
    http_response_code(500); // Error interno del servidor
    echo "Error de conexión con la base de datos.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (!empty($usuario) && !empty($contrasena)) {
        // Consulta preparada para evitar inyección SQL
        $consulta = $enlace->prepare("SELECT * FROM datos WHERE usuario = ? AND contrasena = ?");
        $consulta->bind_param("ss", $usuario, $contrasena);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows == 1) {
            $_SESSION['loggedin'] = true;
            $_SESSION['usuario'] = $usuario;

            // Responder con un mensaje exitoso
            echo "Login exitoso";
        } else {
            http_response_code(401); // No autorizado
            echo "Usuario o contraseña incorrectos.";
        }
        $consulta->close();
    } else {
        http_response_code(400); // Solicitud incorrecta
        echo "Por favor completa todos los campos.";
    }
} else {
    http_response_code(405); // Método no permitido
    echo "Método no permitido.";
}

$enlace->close();
