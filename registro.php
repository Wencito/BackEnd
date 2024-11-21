<?php
// Datos de conexión a la base de datos
$servidor = "localhost";
$usuarioBD = "root";
$clave = "";
$baseDeDatos = "registrar";

$enlace = mysqli_connect($servidor, $usuarioBD, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Procesar el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $confirmarContrasena = $_POST['confirmarContrasena'];

    // Validar que las contraseñas coincidan
    if ($contrasena !== $confirmarContrasena) {
        echo "Las contraseñas no coinciden. Inténtalo de nuevo.";
    } else {
        // Escapar los datos
        $nombre = mysqli_real_escape_string($enlace, $nombre);
        $apellido = mysqli_real_escape_string($enlace, $apellido);
        $usuario = mysqli_real_escape_string($enlace, $usuario);
        $correo = mysqli_real_escape_string($enlace, $correo);
        $contrasenaLimpia = mysqli_real_escape_string($enlace, $contrasena);

        // Verificar duplicados
        $consultaDuplicado = "SELECT * FROM datos WHERE usuario = '$usuario' OR correo = '$correo'";
        $resultadoDuplicado = mysqli_query($enlace, $consultaDuplicado);

        if (mysqli_num_rows($resultadoDuplicado) > 0) {
            echo "El usuario o correo ya están registrados.";
        } else {
            // Guardar datos del usuario
            $insertarDatos = "INSERT INTO datos (nombre, apellido, usuario, correo, contrasena) VALUES ('$nombre', '$apellido', '$usuario', '$correo', '$contrasenaLimpia')";
            if (mysqli_query($enlace, $insertarDatos)) {
                echo "Registro exitoso";
            } else {
                echo "Error en el registro: " . mysqli_error($enlace);
            }
        }
    }
}

// Obtener todos los usuarios registrados
$consultaUsuarios = "SELECT * FROM datos";
$resultadoUsuarios = mysqli_query($enlace, $consultaUsuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antonia Vinos - Usuarios Registrados</title>
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
    <div class="content2">
        <div class="content1">
            <div class="actions-menu shadow">
                <h1>Lista de Usuarios</h1>
                <div class="container mt-4">
                    <table class="table table-hover shadow">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Contraseña</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="lista-contactos">
                            <?php
                            if ($resultadoUsuarios && mysqli_num_rows($resultadoUsuarios) > 0) {
                                while ($usuario = mysqli_fetch_assoc($resultadoUsuarios)) {
                                    echo "<tr>";
                                    echo "<td>" . $usuario['id'] . "</td>";
                                    echo "<td>" . $usuario['nombre'] . "</td>";
                                    echo "<td>" . $usuario['apellido'] . "</td>";
                                    echo "<td>" . $usuario['usuario'] . "</td>";
                                    echo "<td>" . $usuario['correo'] . "</td>";
                                    echo "<td>" . $usuario['contrasena'] . "</td>";
                                    echo "<td>
                                            <button class='btn btn-danger btn-sm' onclick='eliminarUsuario(" . $usuario['id'] . ")'>Eliminar</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No hay usuarios registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function eliminarUsuario(id) {
    console.log("ID a eliminar:", id);
    if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('eliminar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Respuesta del servidor:", data); // Depuración
            alert(data);
            location.reload();
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
mysqli_close($enlace);
?>
