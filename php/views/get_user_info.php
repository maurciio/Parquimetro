<?php
// Configuración de la base de datos
session_start();
require 'conexion.php';
try {
    // Conexión a la base de datos
    $pdo = new PDO($dsn, $user, $pass, $options);

    
    $userId = isset($_GET['rut']) ? $_GET['rut'] : 1; 

    // Consulta preparada para evitar inyección SQL
    $stmt = $pdo->prepare("SELECT estado, nombre, apellido1, apellido2, rut FROM usuarios WHERE rut = :rut");
    $stmt->execute(['rut' => $userId]);

    // Obtener los datos del usuario
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Si se encontró el usuario, devolver los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($usuario);
    } else {
        // Si no se encontró el usuario, devolver un error
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
    }

} catch (\PDOException $e) {
    // En caso de error en la base de datos
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}