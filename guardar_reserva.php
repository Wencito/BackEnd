<?php
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

    // Insertar datos en la base de datos
    $sql = "INSERT INTO reservas (nombre, email, telefono, visitantes, idioma, fecha, bodegas)
            VALUES ('$nombre', '$email', '$telefono', '$visitantes', '$idioma', '$fecha', '$bodegas')";

    if ($conn->query($sql) === TRUE) {
        echo "Reserva guardada con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Manejo de acciones AJAX para borrar o confirmar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $id = $_POST['id'];
    if ($_POST['action'] === 'delete') {
        $sql = "DELETE FROM reservas WHERE id = $id";
        $conn->query($sql);
        echo "Reserva borrada con éxito";
        exit;
    } elseif ($_POST['action'] === 'confirm') {
        $sql = "UPDATE reservas SET confirmada = 1 WHERE id = $id";
        $conn->query($sql);
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
    <title>Antonia Vinos - Lista de Reservas</title>
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
    <div class="content1">
        <div class="actions-menu shadow">
            <h1>Lista Reservas</h1>
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
