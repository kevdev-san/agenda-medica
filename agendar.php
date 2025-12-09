<?php
require_once 'config.php';
verificarSesion();

$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// Obtener adultos mayores del familiar
$stmt = $conn->prepare("SELECT id_adulto_mayor, nombre_completo, parentesco_familiar FROM AdultoMayor WHERE id_familiar = ?");
$stmt->bind_param("i", $id_familiar);
$stmt->execute();
$adultos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener doctores
$doctores = $conn->query("SELECT id_doctor, nombre_completo, especialidad FROM Doctor ORDER BY nombre_completo")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Agendar Cita</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">👴 Agenda Salud</div>
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-link">Volver al Panel</a>
            <div class="perfil-menu">
                <button class="perfil-boton">Mi Perfil ▼</button>
                <div class="dropdown-contenido">
                    <a href="perfil_familiar.php">Perfil/Ajustes de Cuenta</a>
                    <a href="logout.php">Cerrar Sesión (Logout)</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="contenedor-principal">
        <div class="tarjeta-formulario">
            <h2 class="titulo-app">Agendar Nueva Cita</h2>
            <p class="subtitulo">Selecciona los detalles para la cita médica.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alerta-error">
                    <?php 
                        if ($_GET['error'] === 'campos_vacios') echo "⚠ Por favor completa todos los campos";
                        elseif ($_GET['error'] === 'fecha_pasada') echo "⚠ La fecha debe ser futura";
                        elseif ($_GET['error'] === 'horario_ocupado') echo "⚠ El doctor ya tiene una cita en ese horario";
                    ?>
                </div>
            <?php endif; ?>

            <form action="procesar_cita.php" method="POST">
                <div class="grupo-formulario">
                    <label for="adulto_mayor_id">Adulto Mayor</label>
                    <select id="adulto_mayor_id" name="adulto_mayor_id" required>
                        <option value="">-- Seleccione Adulto Mayor --</option>
                        <?php foreach ($adultos as $adulto): ?>
                            <option value="<?php echo $adulto['id_adulto_mayor']; ?>">
                                <?php echo htmlspecialchars($adulto['nombre_completo']); ?> 
                                (<?php echo htmlspecialchars($adulto['parentesco_familiar']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grupo-formulario">
                    <label for="doctor_id">Doctor/Especialidad</label>
                    <select id="doctor_id" name="doctor_id" required>
                        <option value="">-- Seleccione Doctor --</option>
                        <?php foreach ($doctores as $doctor): ?>
                            <option value="<?php echo $doctor['id_doctor']; ?>">
                                <?php echo htmlspecialchars($doctor['nombre_completo']); ?> 
                                (<?php echo htmlspecialchars($doctor['especialidad']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="tipo_cita">Tipo de Cita</label>
                    <select id="tipo_cita" name="tipo_cita" required>
                        <option value="">-- Seleccione Tipo --</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual (Videoconferencia)</option>
                        <option value="Laboratorio">Laboratorio/Estudio</option>
                    </select>
                </div>
                
                <div class="grupo-formulario">
                    <label for="fecha_hora_inicio">Fecha y Hora de la Cita</label>
                    <input type="datetime-local" id="fecha_hora_inicio" name="fecha_hora_inicio" required>
                    <small>Solo se permiten fechas futuras.</small>
                </div>
                
                <div class="grupo-formulario">
                    <label for="notas">Notas (Ej: Síntomas, recordatorios)</label>
                    <textarea id="notas" name="notas" rows="3"></textarea>
                </div>

                <button type="submit" class="boton-primario">Confirmar Cita</button>
            </form>
        </div>
    </main>
</body>
</html>