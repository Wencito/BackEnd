<?php
$servidor = "localhost";
$usuarioBD = "root";
$clave = "";
$baseDeDatos = "registrar";

$enlace = mysqli_connect($servidor, $usuarioBD, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Escapar caracteres especiales para evitar inyección de SQL
    $usuario = mysqli_real_escape_string($enlace, $usuario);
    $contrasena = mysqli_real_escape_string($enlace, $contrasena);

    // Consulta para obtener el usuario y la contraseña
    $consulta = "SELECT * FROM datos WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
    $resultado = mysqli_query($enlace, $consulta);

    if (mysqli_num_rows($resultado) == 1) {
        echo "Login exitoso";
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}

mysqli_close($enlace);
?>

