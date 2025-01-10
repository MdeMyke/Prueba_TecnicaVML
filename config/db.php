<?php
// db.php

// Parámetros de la base de datos
$servername = "localhost";
$username = "root";  // Cambia por tu usuario de la base de datos
$password = "";      // Cambia por tu contraseña de la base de datos
$dbname = "todo_list";  // El nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
