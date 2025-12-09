<?php
require_once 'config.php';
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil_familiar.php");
    exit();
}

$action = $_POST['action'];
$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// ACTUALIZAR INFORMACIÓN PERSONAL
if ($action === 'update_info') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    
    if (empty($nombre) || empty($email)) {
        header("Location: perfil_familiar.php?error=campos_vacios");
        exit();
    }
    
    // Verificar si el email ya existe (excepto el del usuario actual)
    $stmt = $conn->prepare("SELECT id_familiar FROM Familiar WHERE email = ? AND id_familiar != ?");
    $stmt->bind_param("si", $email, $id_familiar);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: perfil_familiar.php?error=email_existe");
        exit();
    }
    $stmt->close();
    
    // Actualizar datos
    $stmt = $conn->prepare("UPDATE Familiar SET nombre_completo = ?, email = ? WHERE id_familiar = ?");
    $stmt->bind_param("ssi", $nombre, $email, $id_familiar);
    
    if ($stmt->execute()) {
        $_SESSION['nombre_completo'] = $nombre;
        $_SESSION['email'] = $email;
        $stmt->close();
        $conn->close();
        header("Location: perfil_familiar.php?mensaje=info_actualizada");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: perfil_familiar.php?error=error_actualizar");
        exit();
    }
}

// ACTUALIZAR CONTRASEÑA
elseif ($action === 'update_password') {
    $password_old = $_POST['password_old'];
    $password_new = $_POST['password_new'];
    $password_confirm = $_POST['password_confirm'];
    
    if (empty($password_old) || empty($password_new) || empty($password_confirm)) {
        header("Location: perfil_familiar.php?error=campos_vacios");
        exit();
    }
    
    if ($password_new !== $password_confirm) {
        header("Location: perfil_familiar.php?error=passwords_no_coinciden");
        exit();
    }
    
    if (strlen($password_new) < 6) {
        header("Location: perfil_familiar.php?error=password_corta");
        exit();
    }
    
    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT contraseña_hash FROM Familiar WHERE id_familiar = ?");
    $stmt->bind_param("i", $id_familiar);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($password_old, $result['contraseña_hash'])) {
        $conn->close();
        header("Location: perfil_familiar.php?error=password_incorrecta");
        exit();
    }
    
    // Actualizar contraseña
    $password_new_hash = password_hash($password_new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE Familiar SET contraseña_hash = ? WHERE id_familiar = ?");
    $stmt->bind_param("si", $password_new_hash, $id_familiar);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: perfil_familiar.php?mensaje=password_actualizada");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: perfil_familiar.php?error=error_actualizar");
        exit();
    }
}

else {
    $conn->close();
    header("Location: perfil_familiar.php");
    exit();
}
?>