<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        header("Location: registro_familiar.html?error=campos_vacios");
        exit();
    }
    
    if ($password !== $password_confirm) {
        header("Location: registro_familiar.html?error=password_no_coincide");
        exit();
    }
    
    if (strlen($password) < 6) {
        header("Location: registro_familiar.html?error=password_corta");
        exit();
    }
    
    $conn = getConnection();
    
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id_familiar FROM Familiar WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: registro_familiar.html?error=email_existe");
        exit();
    }
    $stmt->close();
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo familiar
    $stmt = $conn->prepare("INSERT INTO Familiar (nombre_completo, email, contraseña_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $password_hash);
    
    if ($stmt->execute()) {
        // Guardar ID en sesión para el paso 2
        $_SESSION['id_familiar_temp'] = $conn->insert_id;
        $_SESSION['nombre_familiar_temp'] = $nombre;
        
        $stmt->close();
        $conn->close();
        header("Location: registro_adulto.html");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: registro_familiar.html?error=registro_fallido");
        exit();
    }
}
?>