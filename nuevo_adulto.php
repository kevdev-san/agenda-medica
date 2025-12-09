<?php
require_once 'config.php';
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_am = trim($_POST['nombre_am']);
    $parentesco = trim($_POST['parentesco']);
    $id_familiar = $_SESSION['id_familiar'];
    
    if (empty($nombre_am) || empty($parentesco)) {
        header("Location: nuevo_adulto.php?error=campos_vacios");
        exit();
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO AdultoMayor (nombre_completo, id_familiar, parentesco_familiar) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nombre_am, $id_familiar, $parentesco);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: gestion_adultos.php?mensaje=adulto_agregado");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: nuevo_adulto.php?error=error_registro");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Nuevo Adulto Mayor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <header class="navbar">
        <div class="nav-brand">👴 Agenda Salud</div>
        <nav class="nav-menu">
            <a href="gestion_adultos.php" class="nav-link">Volver a Gestión</a>
        </nav>
    </header>

    <main class="contenedor-principal">
        <div class="tarjeta-formulario">
            <h2 class="titulo-app">Añadir Nuevo Adulto Mayor</h2>
            <p class="subtitulo">Ingresa los datos de la persona mayor.</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alerta-error">
                    <?php 
                        if ($_GET['error'] === 'campos_vacios') echo "⚠ Por favor completa todos los campos";
                        elseif ($_GET['error'] === 'error_registro') echo "⚠ Error al registrar. Intenta nuevamente.";
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grupo-formulario">
                    <label for="nombre_am">Nombre Completo del Adulto Mayor</label>
                    <input type="text" id="nombre_am" name="nombre_am" required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="parentesco">Parentesco (Ej: Padre, Abuela)</label>
                    <input type="text" id="parentesco" name="parentesco" required>
                </div>
                
                <button type="submit" class="boton-primario">Guardar Adulto Mayor</button>
            </form>
        </div>
    </main>
</body>
</html>