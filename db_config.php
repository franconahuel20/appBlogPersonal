<?php
// Configuración de conexión a MariaDB - Contenedor 36339372DB
// Ajustá DB_PASSWORD si usás otra contraseña.
define('DB_SERVER', '172.16.90.146');
define('DB_USERNAME', 'franco');
define('DB_PASSWORD', 'Franco36339372!');
define('DB_NAME', 'db_blog_personal');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
