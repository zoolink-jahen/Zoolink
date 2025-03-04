<?php
$servername = "localhost"; // o la dirección de tu servidor de base de datos
$username = "root"; // tu usuario de base de datos
$password = ""; // tu contraseña de base de datos (si la tienes)
$dbname = "zoolink"; // el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>