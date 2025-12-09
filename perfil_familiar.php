<?php
require_once 'config.php';
verificarSesion();

$conn = getConnection();
$id_familiar = $_SESSION['id_familiar'];

// Obtener datos del familiar
$stmt = $conn->prepare("SELECT nombre_completo, email FROM Familiar WHERE id_familiar = ?");
$stmt->bind_param("i", $id_familiar);
$stmt->execute();
$datos_familiar = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Perfil del Familiar</title>
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
                    <a href="gestion_adultos.php">Gestionar Adultos Mayores</a>
                    <a href="logout.php">Cerrar Sesión (Logout)</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="contenedor-principal">
        <h2 class="titulo-app">Ajustes de Cuenta</h2>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alerta-exito">
                <?php 
                    if ($_GET['mensaje'] === 'info_actualizada') echo "✓ Información actualizada correctamente";
                    elseif ($_GET['mensaje'] === 'password_actualizada') echo "✓ Contraseña cambiada exitosamente";
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alerta-error">
                <?php 
                    if ($_GET['error'] === 'password_incorrecta') echo "⚠ La contraseña actual es incorrecta";
                    elseif ($_GET['error'] === 'passwords_no_coinciden') echo "⚠ Las contraseñas nuevas no coinciden";
                    elseif ($_GET['error'] === 'email_existe') echo "⚠ Ese correo ya está en uso";
                ?>
            </div>
        <?php endif; ?>

        <div class="tarjeta-formulario" style="margin-bottom: 30px;">
            <h3>Información Personal</h3>
            <p class="subtitulo">Edita tu nombre y correo electrónico de acceso.</p>
            
            <form action="procesar_perfil.php" method="POST">
                <input type="hidden" name="action" value="update_info">
                
                <div class="grupo-formulario">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($datos_familiar['nombre_completo']); ?>" required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="email">Correo Electrónico (Tu Usuario)</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($datos_familiar['email']); ?>" required>
                    <small>Se usa para iniciar sesión.</small>
                </div>
                
                <button type="submit" class="boton-primario">Guardar Cambios</button>
            </form>
        </div>
        
        <div class="tarjeta-formulario">
            <h3>Cambiar Contraseña</h3>
            <p class="subtitulo">Asegúrate de usar una contraseña segura.</p>

            <form action="procesar_perfil.php" method="POST">
                <input type="hidden" name="action" value="update_password">

                <div class="grupo-formulario">
                    <label for="password_old">Contraseña Actual</label>
                    <input type="password" id="password_old" name="password_old" required>
                    <small>Necesaria para verificar tu identidad.</small>
                </div>

                <div class="grupo-formulario">
                    <label for="password_new">Nueva Contraseña</label>
                    <input type="password" id="password_new" name="password_new" required>
                </div>

                <div class="grupo-formulario">
                    <label for="password_confirm">Confirmar Nueva Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <button type="submit" class="boton-primario boton-peligro">Actualizar Contraseña</button>
            </form>
        </div>
    </main>
</body>
</html>