<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        header("Location: login.html?error=campos_vacios");
        exit();
    }
    
    $conn = getConnection();
    
    // Buscar usuario por email
    $stmt = $conn->prepare("SELECT id_familiar, nombre_completo, contraseña_hash FROM Familiar WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña
        if (password_verify($password, $usuario['contraseña_hash'])) {
            // Login exitoso
            $_SESSION['id_familiar'] = $usuario['id_familiar'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['email'] = $email;
            
            header("Location: dashboard.php");
            exit();
        }
    }
    
    // Credenciales incorrectas
    $stmt->close();
    $conn->close();
    header("Location: login.html?error=credenciales_invalidas");
    exit();
}
?>