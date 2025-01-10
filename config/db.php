<?php

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "todo_list";  

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
