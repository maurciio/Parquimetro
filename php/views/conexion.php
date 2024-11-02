<?php
$servername = "localhost";
$username = "root";
$password = ""; // Contraseña de tu base de datos
$dbname = "parquimetros"; // Nombre de tu base de datos
 
// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);
 
// Verificar conexión
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
 
?>