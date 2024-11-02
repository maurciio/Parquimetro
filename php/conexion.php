<?php
$servername = "localhost";
$username = "root";
$password = "leica666"; // Contraseña de tu base de datos
$dbname = "parquimetros"; // Nombre de tu base de datos
 
// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);
 
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
 
?>