<?php
require_once 'config.php';
verificarSesion();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id_cita = $_GET['id'];
$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// Verificar que la cita pertenece al familiar antes de eliminar
$query = "
    SELECT c.id_cita 
    FROM Cita c
    INNER JOIN AdultoMayor am ON c.id_adulto_mayor = am.id_adulto_mayor
    WHERE c.id_cita = ? AND am.id_familiar = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_cita, $id_familiar);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}
$stmt->close();

// Eliminar la cita
$stmt = $conn->prepare("DELETE FROM Cita WHERE id_cita = ?");
$stmt->bind_param("i", $id_cita);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?mensaje=cita_eliminada");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?error=error_eliminar");
    exit();
}
?>