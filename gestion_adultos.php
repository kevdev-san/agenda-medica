<?php
require_once 'config.php';
verificarSesion();

$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// Obtener todos los adultos mayores del familiar
$stmt = $conn->prepare("SELECT id_adulto_mayor, nombre_completo, parentesco_familiar FROM AdultoMayor WHERE id_familiar = ? ORDER BY nombre_completo");
$stmt->bind_param("i", $id_familiar);
$stmt->execute();
$adultos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Gestión de Adultos Mayores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
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
        <div class="header-gestion">
            <h2>Gestión de Pacientes</h2>
            <a href="nuevo_adulto.php" class="boton-primario boton-pequeno">
                + Añadir Nuevo Adulto Mayor
            </a>
        </div>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alerta-exito">
                <?php 
                    if ($_GET['mensaje'] === 'adulto_agregado') echo "✓ Adulto mayor agregado exitosamente";
                    elseif ($_GET['mensaje'] === 'adulto_editado') echo "✓ Datos actualizados correctamente";
                    elseif ($_GET['mensaje'] === 'adulto_eliminado') echo "✓ Adulto mayor desvinculado";
                ?>
            </div>
        <?php endif; ?>

        <p>A continuación, se muestran todas las personas mayores vinculadas a tu cuenta.</p>

        <section class="lista-adultos">
            <?php if (count($adultos) === 0): ?>
                <p>No tienes adultos mayores registrados. <a href="nuevo_adulto.php">Añade uno aquí</a>.</p>
            <?php else: ?>
                <?php $contador = 1; ?>
                <?php foreach ($adultos as $adulto): ?>
                    <div class="tarjeta-adulto">
                        <div class="info-adulto">
                            <span class="nombre-adulto">
                                <?php echo $contador++; ?>. <?php echo htmlspecialchars($adulto['nombre_completo']); ?>
                            </span>
                            <span class="parentesco-adulto">
                                Parentesco: <?php echo htmlspecialchars($adulto['parentesco_familiar']); ?>
                            </span>
                        </div>
                        <div class="acciones-adulto">
                            <a href="editar_adulto.php?id=<?php echo $adulto['id_adulto_mayor']; ?>" class="boton-secundario">Editar</a>
                            <a href="eliminar_adulto.php?id=<?php echo $adulto['id_adulto_mayor']; ?>" 
                               class="boton-eliminar"
                               onclick="return confirm('¿Estás seguro de desvincular a este adulto mayor?');">Desvincular</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>