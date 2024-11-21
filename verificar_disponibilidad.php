<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Datos de conexión a la base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "reservas";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fecha = $_POST['fecha'];
    $bodegas = $_POST['bodegas'];

    // Establecer capacidad máxima
    $capacidad_maxima = 12;

    // Consultar cuántas reservas ya existen para esa fecha y bodega
    $sql_check = "SELECT COUNT(*) AS total FROM reservas WHERE fecha = '$fecha' AND bodegas = '$bodegas'";
    $result_check = $conn->query($sql_check);
    $row = $result_check->fetch_assoc();
    $reservas_actuales = $row['total'];

    if ($reservas_actuales >= $capacidad_maxima) {
        echo "No hay disponibilidad";
    } else {
        echo "Hay disponibilidad";
    }
}

// Cerrar conexión
$conn->close();
?>
