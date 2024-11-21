<?php
// Configuración de conexión a la base de datos
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
session_start();

// Redirigir al login si no está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Función para obtener las bodegas más buscadas
function obtenerBodegasMasBuscadas($conn) {
    $sql = "SELECT bodegas, COUNT(*) AS visitas 
            FROM reservas 
            GROUP BY bodegas 
            ORDER BY visitas DESC 
            LIMIT 5";
    $resultado = $conn->query($sql);

    $bodegas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $bodegas[] = $fila;
        }
    }
    return $bodegas;
}

// Función para traducir meses al español
function traducirMes($mes) {
    $meses = [
        1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
        5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
        9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
    ];
    return $meses[$mes];
}

// Obtener estadísticas
$bodegas_mas_buscadas = obtenerBodegasMasBuscadas($conn);

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antonia Vinos | Estadísticas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">

    <style>
        /* General */
        body {
            display: flex;
            margin: 0;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
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
            height: 100vh;
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

        /* Contenido */
        .content1 {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
            overflow-y: auto;
            margin: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .calendar-container {
            margin-bottom: 30px;
        }
        .calendar h4 {
            background-color: #f8f9fa;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        .day {
            padding: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            background-color: #e9ecef;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .day:hover {
            background-color: #ffc107;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
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
        <h3 class="text-center">Reservas realizadas</h3>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Bodega</th>
                    <th>Visitas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bodegas_mas_buscadas as $bodega): ?>
                    <tr class="bodega" data-bodega="<?= htmlspecialchars($bodega['bodegas']); ?>">
                        <td><?= htmlspecialchars($bodega['bodegas']); ?></td>
                        <td><?= htmlspecialchars($bodega['visitas']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3 class="text-center mt-5">Calendario de Disponibilidad</h3>
        <?php
        $meses = [11, 12, 1];
        $anio_actual = date("Y");
        foreach ($meses as $mes):
            $anio = ($mes === 1) ? $anio_actual + 1 : $anio_actual;
            $dias_del_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
            $nombre_mes = traducirMes($mes);
        ?>
        <div class="calendar-container">
            <h4><?= "$nombre_mes $anio" ?></h4>
            <div class="calendar">
                <?php for ($dia = 1; $dia <= $dias_del_mes; $dia++): ?>
                    <div class="day" data-fecha="<?= sprintf("%04d-%02d-%02d", $anio, $mes, $dia); ?>">
                        <?= $dia; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <div id="detalle" class="mt-4">
            <h4 class="text-center">Detalles</h4>
            <div id="detalle-contenido" class="mt-3"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.day').on('click', function () {
                const fecha = $(this).data('fecha');
                obtenerDetalles('fecha', fecha);
            });

            $('.bodega').on('click', function () {
                const bodega = $(this).data('bodega');
                obtenerDetalles('bodega', bodega);
            });

            function obtenerDetalles(tipo, valor) {
                $.post('detalle.php', { tipo, valor }, function (respuesta) {
                    $('#detalle-contenido').html(respuesta);
                }).fail(function () {
                    $('#detalle-contenido').html('<p class="text-danger">Error al cargar detalles.</p>');
                });
            }
        });
    </script>
</body>
</html>
