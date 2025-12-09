<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que venimos del paso 1
    if (!isset($_SESSION['id_familiar_temp'])) {
        header("Location: registro_familiar.html?error=sesion_invalida");
        exit();
    }
    
    $nombre_am = trim($_POST['nombre_am']);
    $parentesco = trim($_POST['parentesco']);
    $id_familiar = $_SESSION['id_familiar_temp'];
    
    if (empty($nombre_am) || empty($parentesco)) {
        header("Location: registro_adulto.html?error=campos_vacios");
        exit();
    }
    
    $conn = getConnection();
    
    // Insertar adulto mayor
    $stmt = $conn->prepare("INSERT INTO AdultoMayor (nombre_completo, id_familiar, parentesco_familiar) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nombre_am, $id_familiar, $parentesco);
    
    if ($stmt->execute()) {
        // Registro completo: iniciar sesión automáticamente
        $_SESSION['id_familiar'] = $id_familiar;
        $_SESSION['nombre_completo'] = $_SESSION['nombre_familiar_temp'];
        
        // Limpiar variables temporales
        unset($_SESSION['id_familiar_temp']);
        unset($_SESSION['nombre_familiar_temp']);
        
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php?registro=exitoso");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: registro_adulto.html?error=registro_fallido");
        exit();
    }
}
?>