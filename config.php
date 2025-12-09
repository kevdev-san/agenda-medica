<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Cambia según tu configuración
define('DB_PASS', ''); // Cambia según tu configuración
define('DB_NAME', 'agenda_adultos_mayores');

// Crear conexión
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para verificar si el usuario está autenticado
function verificarSesion() {
    if (!isset($_SESSION['id_familiar'])) {
        header("Location: login.html");
        exit();
    }
}

// Función para cerrar sesión
function cerrarSesion() {
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}
?>