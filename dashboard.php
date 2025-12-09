<?php
require_once 'config.php';
verificarSesion();

$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// Obtener todas las citas de los adultos mayores vinculados a este familiar
$query = "
    SELECT 
        c.id_cita,
        c.fecha_hora_inicio,
        c.tipo_cita,
        c.estado_cita,
        c.notas,
        am.nombre_completo AS nombre_adulto,
        d.nombre_completo AS nombre_doctor,
        d.especialidad
    FROM Cita c
    INNER JOIN AdultoMayor am ON c.id_adulto_mayor = am.id_adulto_mayor
    INNER JOIN Doctor d ON c.id_doctor = d.id_doctor
    WHERE am.id_familiar = ?
    ORDER BY c.fecha_hora_inicio ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_familiar);
$stmt->execute();
$result = $stmt->get_result();
$citas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Panel Principal</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body class="dashboard-page">
    <header class="navbar">
        <div class="nav-brand">👴 Agenda Salud</div>
        <nav class="nav-menu">
            <a href="agendar.php" class="nav-link">Agendar Cita</a>
            <div class="perfil-menu">
                <button class="perfil-boton">Mi Perfil ▼</button>
                <div class="dropdown-contenido">
                    <a href="perfil_familiar.php">Perfil/Ajustes de Cuenta</a>
                    <a href="gestion_adultos.php">Gestionar Adultos Mayores</a>
                    <a href="logout.php">Cerrar Sesión (Logout)</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="contenedor-principal">
        <h2>📅 Mis Próximas Citas Médicas</h2>
        <p>Aquí se muestran las citas agendadas para los adultos mayores.</p>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alerta-exito">
                <?php 
                    if ($_GET['mensaje'] === 'cita_creada') echo "✓ Cita agendada exitosamente";
                    elseif ($_GET['mensaje'] === 'cita_editada') echo "✓ Cita actualizada correctamente";
                    elseif ($_GET['mensaje'] === 'cita_eliminada') echo "✓ Cita eliminada";
                ?>
            </div>
        <?php endif; ?>

        <section class="lista-citas">
            <?php if (count($citas) === 0): ?>
                <p>No hay citas agendadas. <a href="agendar.php">Agenda tu primera cita aquí</a>.</p>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <div class="tarjeta-cita">
                        <div class="cita-header">
                            <span class="cita-titulo">
                                <?php echo htmlspecialchars($cita['nombre_adulto']); ?> - 
                                <?php echo htmlspecialchars($cita['nombre_doctor']); ?> 
                                (<?php echo htmlspecialchars($cita['especialidad']); ?>)
                            </span>
                            <span class="cita-tipo tipo-<?php echo strtolower($cita['tipo_cita']); ?>">
                                <?php echo htmlspecialchars($cita['tipo_cita']); ?>
                            </span>
                        </div>
                        <p class="cita-detalle">
                            <strong>Fecha:</strong> 
                            <?php 
                                $fecha = new DateTime($cita['fecha_hora_inicio']);
                                echo $fecha->format('d \d\e F \d\e Y, g:i A');
                            ?>
                        </p>
                        <?php if (!empty($cita['notas'])): ?>
                            <p class="cita-detalle"><strong>Notas:</strong> <?php echo htmlspecialchars($cita['notas']); ?></p>
                        <?php endif; ?>
                        <div class="cita-acciones">
                            <a href="editar_cita.php?id=<?php echo $cita['id_cita']; ?>" class="boton-secundario">Editar</a>
                            <a href="eliminar_cita.php?id=<?php echo $cita['id_cita']; ?>" 
                               class="boton-eliminar"
                               onclick="return confirm('¿Estás seguro de eliminar esta cita?');">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>