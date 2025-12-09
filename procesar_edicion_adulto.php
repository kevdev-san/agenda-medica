<?php
require_once 'config.php';
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adulto_id = $_POST['adulto_id'];
    $nombre_am = trim($_POST['nombre_am']);
    $parentesco = trim($_POST['parentesco']);
    $id_familiar = $_SESSION['id_familiar'];
    
    if (empty($nombre_am) || empty($parentesco)) {
        header("Location: editar_adulto.php?id=$adulto_id&error=campos_vacios");
        exit();
    }
    
    $conn = getConnection();
    
    // Verificar que el adulto pertenece al familiar
    $stmt = $conn->prepare("SELECT id_adulto_mayor FROM AdultoMayor WHERE id_adulto_mayor = ? AND id_familiar = ?");
    $stmt->bind_param("ii", $adulto_id, $id_familiar);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: gestion_adultos.php?error=acceso_denegado");
        exit();
    }
    $stmt->close();
    
    // Actualizar datos
    $stmt = $conn->prepare("UPDATE AdultoMayor SET nombre_completo = ?, parentesco_familiar = ? WHERE id_adulto_mayor = ?");
    $stmt->bind_param("ssi", $nombre_am, $parentesco, $adulto_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: gestion_adultos.php?mensaje=adulto_editado");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: editar_adulto.php?id=$adulto_id&error=error_actualizar");
        exit();
    }
}
?>