<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

header('Content-Type: text/html');

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

// Iniciar sesión

// Verificar si se ha enviado un formulario de reserva
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'])) {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $visitantes = $_POST['visitantes'];
    $idioma = $_POST['idioma'];
    $fecha = $_POST['fecha'];
    $bodegas = $_POST['bodegas'];

    $capacidad_maxima = 10;

    // Comprobar disponibilidad
    $sql_check = "SELECT COUNT(*) AS total FROM reservas WHERE fecha = ? AND bodegas = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $fecha, $bodegas);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();
    $reservas_actuales = $row['total'] ?? 0;

    // Validar capacidad
    if ($reservas_actuales >= $capacidad_maxima) {
        echo "No hay disponibilidad para la fecha seleccionada.";
        exit;
    }

    // Insertar datos en la base de datos
    $sql = "INSERT INTO reservas (nombre, email, telefono, visitantes, idioma, fecha, bodegas)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nombre, $email, $telefono, $visitantes, $idioma, $fecha, $bodegas);

    if ($stmt->execute()) {
        echo "Reserva guardada con éxito";
    } else {
        echo "Error al guardar la reserva: " . $conn->error;
    }

    exit; // Salir después de procesar la solicitud
}

// Manejo de acciones AJAX para borrar o confirmar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    if ($_POST['action'] === 'delete') {
        $sql = "DELETE FROM reservas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "Reserva borrada con éxito";
        exit;
    } elseif ($_POST['action'] === 'confirm') {
        $sql = "UPDATE reservas SET confirmada = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "Reserva confirmada con éxito";
        exit;
    }
}

// Consulta para obtener todas las reservas
$sql = "SELECT * FROM reservas";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antonia Vinos | Lista de reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
        <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">

        <style>
        /* General Styles */
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
        }
        * {
            box-sizing: border-box;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 15px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #495057;
        }
        .btn-logout {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }

        /* Main Content */
        .content1 {
            flex-grow: 1;
            padding: 20px;
        }
        .actions-menu {
            padding: 20px;
            background-color: #bbb;
            color: #333;
            border-radius: 20px;
        }
        .actions-menu h3 {
            margin-bottom: 20px;
            text-align: center;
        }

        /* Table Styles */
        .table {
            border-radius: 5px;
            overflow: hidden;
        }
        .thead-dark th {
            background-color: #0F4C75;
            color: white;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Footer */
        .Footer1 {
            background-color: #354046;
            height: 82px;
            width: 100%;
            margin-top: 50px;
        }
        .LetrasFoot {
            color: white;
            justify-content: center;
            margin: 20px;
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #17a2b8;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            display: none;
        }
        .btn-logout {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>
    <div class="sidebar">
    <div>
            <h2>Menú</h2>
            <ul>
                <li><a href="registro.php" class="active">Usuarios</a></li>
                <li><a href="reserva.php">Reservas</a></li>
                <li><a href="estadisticas.php">Estadísticas</a></li>
            </ul>
        </div>
        <button class="btn-logout" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
    </div>
    <div id="notification" class="notification">
        Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!
    </div>
    <div class="content1">
        <div class="actions-menu shadow">
            <center><h3>Reservas Existentes</h3></center>
            <div class="container mt-4">
                <table class="table table-hover shadow">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Email</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Visitantes</th>
                            <th scope="col">Idioma</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Bodega</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-contactos">
                        <?php
                        // Mostrar cada reserva en una fila de la tabla
                        if ($resultado->num_rows > 0) {
                            while($fila = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $fila["id"] . "</td>";
                                echo "<td>" . $fila["nombre"] . "</td>";
                                echo "<td>" . $fila["email"] . "</td>";
                                echo "<td>" . $fila["telefono"] . "</td>";
                                echo "<td>" . $fila["visitantes"] . "</td>";
                                echo "<td>" . $fila["idioma"] . "</td>";
                                echo "<td>" . $fila["fecha"] . "</td>";
                                echo "<td>" . $fila["bodegas"] . "</td>";
                                echo "<td>
                                        <button onclick=\"accionReserva('delete', " . $fila["id"] . ")\" class='btn btn-danger btn-sm'>Borrar</button>
                                        <button onclick=\"accionReserva('confirm', " . $fila["id"] . ")\" class='btn btn-success btn-sm'>Confirmar</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No hay reservas registradas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Función para manejar la acción de borrar o confirmar
        function accionReserva(action, id) {
            if (confirm("¿Estás seguro de que deseas " + (action === "delete" ? "borrar" : "confirmar") + " esta reserva?")) {
                // Solicitud AJAX
                const formData = new FormData();
                formData.append('action', action);
                formData.append('id', id);

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Muestra el mensaje de éxito
                    location.reload(); // Recarga la página para actualizar la tabla
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
