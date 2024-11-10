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
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <style>
        body {
    display: flex;
    font-family: Arial, sans-serif;
    margin: 0;
    height: 100vh;
}

.sidebar {
    width: 250px;
    height: 127vh; 
    background-color: #cccc;
    color: rgb(0, 0, 0);
    padding: 20px;
    box-sizing: border-box;
}


.sidebar h2 {
    text-align: center;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin: 20px 0;
}

.sidebar ul li a {
    color: black;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 5px;
}

.sidebar ul li a:hover {
    background-color: #444;
    color: white;
}

.main-content {
    flex: 1;
    padding: 20px;
}

.content {
    display: none;
}

.content.active {
    display: block;
}

.login_logo {
    text-align: center;
    margin: 0 auto; 
    padding: 20px 0;
}

.login_logo img {
    max-width: 250px;
    height: auto; 
}

.m-aside-menu .m-menu__nav>.m-menu__item {
    position: relative;
    margin: 0;
}
.m-aside-menu .m-menu__nav .m-menu__item {
    display: block;
    float: none;
    height: auto;
    padding: 0;
}
*, ::after, ::before {
    box-sizing: border-box;
}

li {
    display: list-item;
    text-align: -webkit-match-parent;
    unicode-bidi: isolate;
}

.m-aside-menu .m-menu__nav {
    list-style: none;
    padding: 30px 0 30px 0;
}


ul {
    list-style-type: disc;
}

.actions-menu {
    margin-top: 20px;
    flex: 1; 
    padding: 20px;  
    background-color: #bbb; 
    color: #333;
    min-height: 80vh; 
    border-radius: 20px;
}

.actions-menu h1 {
    font-size: 40px; 
    margin-bottom: 20px; 
    text-align: center;
    color: #333; 
}

.search-container {
    display: flex; 
    align-items: center;
    margin: 20px 0; 
}

.search-container .form-control {
    border-radius: 5px;
    border: 1px solid #000000;
    flex: 1;
    padding: 10px; 
}

.search-container .btn {
    border-radius: 5px;
    margin-left: 0;
    color: black;
}


@media (min-width: 768px) {
    @supports ((position:-webkit-sticky) or (position:sticky)) {
        .bd-navbar1 {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1071;
        }
    }
}

.navbar1-expand {
    -ms-flex-flow: row nowrap;
    flex-flow: row nowrap;
    -ms-flex-pack: start;
    justify-content: flex-start;
}

.navbar1 {
    position: sticky; 
    top: 0; 
    max-width: 80%;
    background-color: #BBE1FA;
    z-index: 1000; 
    height: 150px;
    margin-bottom: 200px;
}

.container {
    margin-top: 20px;
}

.table {
    margin-top: 100px; 
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

.content1{
    width: 1000px;
    height: 500px;
    margin-bottom: 150px;
    margin-left: 90px;
    margin-right: 60px;
    padding: 20px;
    flex-grow: 1; 
    margin-top: 30px;
}


.EspacioOscuro {
    background-color: #1B262C;
    flex-grow: 1; 
    margin: 0; 
    height:100px;
    width: 1300px;
}

.Footer1 {
    background-color: #354046;
    flex-grow: 1; 
    margin: 0; 
    height: 82px;
    width: 100%;
    margin-top: 50px;
}

.modal-header {
    background-color: #aaa; 
    color: white;
}

.modal-title {
    font-size: 1.5rem;
}

.modal-body {
    padding: 20px;
}

.form-group label {
    font-weight: bold;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
}

.btn-primary {
    background-color: #007bff; 
    border-color: #007bff; 
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.Obstaculo{
    width: 150px;
    height: 70px;
}

.upload-container {
    display: inline-block; 
    margin: 0; 
    padding: 0;
    margin-left: 10px;
}

.upload-container label {
    display: inline-block; 
    margin: 0;
}


.botonExcel {
    background-color: #4CAF50 !important; 
    color: rgb(230, 221, 221) !important; 
    border: none; 
    transition: background-color 0.3s; 
    padding: 5px 10px; 
    margin: 0;
}

.botonExcel:hover {
    background-color: #6dce72 !important;
    color: white !important;
}

.botonAgregar {
    background-color: #0c3e7d !important; 
    color: rgb(230, 221, 221) !important; 
    border: none; 
    transition: background-color 0.3s; 
    margin: 0;
}

.botonAgregar:hover {
    background-color: #3b8be0 !important;
    color: white !important;
}

.Footer1 {
    background-color: #354046;
    flex-grow: 1; 
    margin: 0; 
    height: 82px;
    width: 100%;
    margin-top: 50px;
}

.LetrasFoot{
    color: white;
    justify-content: center;
    margin-left: 20px;
    margin-top: 30px;
}
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menú</h2>
        <ul>
        <li><a href="http://localhost/ejemplo/registro.php">Usuarios</a></li>
            <li><a href="http://localhost/ejemplo/guardar_reserva.php">Reservas</a></li>
            <li><a href="#">Estadísticas</a></li>
        </ul>
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
                                            <button class='btn btn-primary btn-sm' onclick='confirmarUsuario(" . $usuario['id'] . ")'>Confirmar</button>
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
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function confirmarUsuario(id) {
            alert("Usuario confirmado: " + id);
            // Implementa la acción de confirmación aquí si es necesario
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
