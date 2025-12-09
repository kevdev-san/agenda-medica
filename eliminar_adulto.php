<?php
require_once 'config.php';
verificarSesion();

if (!isset($_GET['id'])) {
    header("Location: gestion_adultos.php");
    exit();
}

$adulto_id = $_GET['id'];
$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

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

// Verificar si tiene citas asociadas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM Cita WHERE id_adulto_mayor = ?");
$stmt->bind_param("i", $adulto_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($result['total'] > 0) {
    $conn->close();
    header("Location: gestion_adultos.php?error=tiene_citas");
    exit();
}

// Eliminar adulto mayor
$stmt = $conn->prepare("DELETE FROM AdultoMayor WHERE id_adulto_mayor = ?");
$stmt->bind_param("i", $adulto_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: gestion_adultos.php?mensaje=adulto_eliminado");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: gestion_adultos.php?error=error_eliminar");
    exit();
}
?>