<?php
require_once 'config.php';
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adulto_mayor_id = $_POST['adulto_mayor_id'];
    $doctor_id = $_POST['doctor_id'];
    $tipo_cita = $_POST['tipo_cita'];
    $fecha_hora_inicio = $_POST['fecha_hora_inicio'];
    $notas = trim($_POST['notas']);
    
    // Validaciones
    if (empty($adulto_mayor_id) || empty($doctor_id) || empty($tipo_cita) || empty($fecha_hora_inicio)) {
        header("Location: agendar.php?error=campos_vacios");
        exit();
    }
    
    // Validar que la fecha sea futura
    $fecha_cita = new DateTime($fecha_hora_inicio);
    $fecha_actual = new DateTime();
    
    if ($fecha_cita <= $fecha_actual) {
        header("Location: agendar.php?error=fecha_pasada");
        exit();
    }
    
    $conn = getConnection();
    
    // Verificar que el adulto mayor pertenece al familiar autenticado
    $stmt = $conn->prepare("SELECT id_adulto_mayor FROM AdultoMayor WHERE id_adulto_mayor = ? AND id_familiar = ?");
    $stmt->bind_param("ii", $adulto_mayor_id, $_SESSION['id_familiar']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header("Location: agendar.php?error=acceso_denegado");
        exit();
    }
    $stmt->close();
    
    // Insertar la cita
    $stmt = $conn->prepare("INSERT INTO Cita (id_adulto_mayor, id_doctor, fecha_hora_inicio, tipo_cita, notas) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $adulto_mayor_id, $doctor_id, $fecha_hora_inicio, $tipo_cita, $notas);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php?mensaje=cita_creada");
        exit();
    } else {
        // Error de duplicado (doctor ya tiene cita en ese horario)
        if ($conn->errno === 1062) {
            $stmt->close();
            $conn->close();
            header("Location: agendar.php?error=horario_ocupado");
            exit();
        }
        
        $stmt->close();
        $conn->close();
        header("Location: agendar.php?error=error_general");
        exit();
    }
}
?>