<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "reservas";

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];

    if ($tipo === 'fecha') {
        $sql = "SELECT * FROM reservas WHERE fecha = ?";
    } elseif ($tipo === 'bodega') {
        $sql = "SELECT * FROM reservas WHERE bodegas = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<ul>";
        while ($fila = $resultado->fetch_assoc()) {
            echo "<li>{$fila['nombre']} ({$fila['email']}), Tel: {$fila['telefono']}, Fecha: {$fila['fecha']}</li>";
        }
        echo "</ul>";
    } else {
        echo "No hay reservas para este criterio.";
    }
    $stmt->close();
}

$conn->close();
?>
