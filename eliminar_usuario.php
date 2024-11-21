<?php
// Datos de conexión a la base de datos
$servidor = "localhost";
$usuarioBD = "root";
$clave = "";
$baseDeDatos = "registrar";

// Conexión con la base de datos
$conexion = mysqli_connect($servidor, $usuarioBD, $clave, $baseDeDatos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        
        // Verificar conexión
        if ($conexion->connect_error) {
            echo "Error de conexión a la base de datos: " . $conexion->connect_error;
            exit();
        }
        
        // Cambiar 'usuarios' a 'datos' (nombre correcto de la tabla)
        $sql = "DELETE FROM datos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "Usuario eliminado exitosamente.";
            } else {
                echo "Error al eliminar el usuario: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conexion->error;
        }

        $conexion->close();
    } else {
        echo "Parámetros inválidos.";
    }
} else {
    echo "Método no permitido.";
}
?>
