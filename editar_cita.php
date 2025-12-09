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

// Obtener datos de la cita (verificar que pertenece al familiar)
$query = "
    SELECT c.*, am.id_familiar 
    FROM Cita c
    INNER JOIN AdultoMayor am ON c.id_adulto_mayor = am.id_adulto_mayor
    WHERE c.id_cita = ? AND am.id_familiar = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_cita, $id_familiar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}

$cita = $result->fetch_assoc();
$stmt->close();

// Obtener adultos mayores y doctores para los selectores
$stmt = $conn->prepare("SELECT id_adulto_mayor, nombre_completo, parentesco_familiar FROM AdultoMayor WHERE id_familiar = ?");
$stmt->bind_param("i", $id_familiar);
$stmt->execute();
$adultos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$doctores = $conn->query("SELECT id_doctor, nombre_completo, especialidad FROM Doctor")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Editar Cita</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">👴 Agenda Salud</div>
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-link">Volver al Panel</a>
        </nav>
    </header>

    <main class="contenedor-principal">
        <div class="tarjeta-formulario">
            <h2 class="titulo-app">Modificar Cita Existente</h2>
            <p class="subtitulo">ID de Cita: #<?php echo $id_cita; ?></p>

            <form action="procesar_edicion_cita.php" method="POST">
                <input type="hidden" name="cita_id" value="<?php echo $id_cita; ?>">
                
                <div class="grupo-formulario">
                    <label for="adulto_mayor_id">Adulto Mayor</label>
                    <select id="adulto_mayor_id" name="adulto_mayor_id" required>
                        <?php foreach ($adultos as $adulto): ?>
                            <option value="<?php echo $adulto['id_adulto_mayor']; ?>"
                                <?php echo ($adulto['id_adulto_mayor'] == $cita['id_adulto_mayor']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($adulto['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grupo-formulario">
                    <label for="doctor_id">Doctor/Especialidad</label>
                    <select id="doctor_id" name="doctor_id" required>
                        <?php foreach ($doctores as $doctor): ?>
                            <option value="<?php echo $doctor['id_doctor']; ?>"
                                <?php echo ($doctor['id_doctor'] == $cita['id_doctor']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['nombre_completo']) . ' (' . htmlspecialchars($doctor['especialidad']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="tipo_cita">Tipo de Cita</label>
                    <select id="tipo_cita" name="tipo_cita" required>
                        <option value="Presencial" <?php echo ($cita['tipo_cita'] == 'Presencial') ? 'selected' : ''; ?>>Presencial</option>
                        <option value="Virtual" <?php echo ($cita['tipo_cita'] == 'Virtual') ? 'selected' : ''; ?>>Virtual</option>
                        <option value="Laboratorio" <?php echo ($cita['tipo_cita'] == 'Laboratorio') ? 'selected' : ''; ?>>Laboratorio</option>
                    </select>
                </div>
                
                <div class="grupo-formulario">
                    <label for="fecha_hora_inicio">Fecha y Hora de la Cita</label>
                    <input type="datetime-local" id="fecha_hora_inicio" name="fecha_hora_inicio" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_hora_inicio'])); ?>" required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="estado_cita">Estado</label>
                    <select id="estado_cita" name="estado_cita" required>
                        <option value="Agendada" <?php echo ($cita['estado_cita'] == 'Agendada') ? 'selected' : ''; ?>>Agendada</option>
                        <option value="Completada" <?php echo ($cita['estado_cita'] == 'Completada') ? 'selected' : ''; ?>>Completada</option>
                        <option value="Cancelada" <?php echo ($cita['estado_cita'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                        <option value="Pendiente" <?php echo ($cita['estado_cita'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    </select>
                </div>
                
                <div class="grupo-formulario">
                    <label for="notas">Notas</label>
                    <textarea id="notas" name="notas" rows="3"><?php echo htmlspecialchars($cita['notas']); ?></textarea>
                </div>

                <button type="submit" class="boton-primario">Guardar Cambios</button>
            </form>
        </div>
    </main>
</body>
</html>