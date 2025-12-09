<?php
require_once 'config.php';
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cita_id = $_POST['cita_id'];
    $adulto_mayor_id = $_POST['adulto_mayor_id'];
    $doctor_id = $_POST['doctor_id'];
    $tipo_cita = $_POST['tipo_cita'];
    $fecha_hora_inicio = $_POST['fecha_hora_inicio'];
    $estado_cita = $_POST['estado_cita'];
    $notas = trim($_POST['notas']);
    
    $conn = getConnection();
    $id_familiar = $_SESSION['id_familiar'];
    
    // Verificar que la cita pertenece al familiar
    $query = "
        SELECT c.id_cita 
        FROM Cita c
        INNER JOIN AdultoMayor am ON c.id_adulto_mayor = am.id_adulto_mayor
        WHERE c.id_cita = ? AND am.id_familiar = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cita_id, $id_familiar);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php?error=acceso_denegado");
        exit();
    }
    $stmt->close();
    
    // Actualizar la cita
    $stmt = $conn->prepare("
        UPDATE Cita 
        SET id_adulto_mayor = ?, id_doctor = ?, tipo_cita = ?, fecha_hora_inicio = ?, estado_cita = ?, notas = ?
        WHERE id_cita = ?
    ");
    $stmt->bind_param("iissssi", $adulto_mayor_id, $doctor_id, $tipo_cita, $fecha_hora_inicio, $estado_cita, $notas, $cita_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php?mensaje=cita_editada");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: editar_cita.php?id=$cita_id&error=error_actualizar");
        exit();
    }
}
?>