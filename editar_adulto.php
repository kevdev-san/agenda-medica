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

// Obtener datos del adulto mayor (verificar que pertenece al familiar)
$stmt = $conn->prepare("SELECT * FROM AdultoMayor WHERE id_adulto_mayor = ? AND id_familiar = ?");
$stmt->bind_param("ii", $adulto_id, $id_familiar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: gestion_adultos.php?error=acceso_denegado");
    exit();
}

$datos_adulto = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Médica - Editar Adulto Mayor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">👴 Agenda Salud</div>
        <nav class="nav-menu">
            <a href="gestion_adultos.php" class="nav-link">Volver a Gestión</a>
        </nav>
    </header>

    <main class="contenedor-principal">
        <div class="tarjeta-formulario">
            <h2 class="titulo-app">Editar Datos del Adulto Mayor</h2>
            <p class="subtitulo">Modifica el nombre o parentesco de <strong><?php echo htmlspecialchars($datos_adulto['nombre_completo']); ?></strong>.</p>
            
            <form action="procesar_edicion_adulto.php" method="POST">
                <input type="hidden" name="adulto_id" value="<?php echo $adulto_id; ?>">
                
                <div class="grupo-formulario">
                    <label for="nombre_am">Nombre Completo del Adulto Mayor</label>
                    <input type="text" id="nombre_am" name="nombre_am" 
                           value="<?php echo htmlspecialchars($datos_adulto['nombre_completo']); ?>" required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="parentesco">Parentesco (Ej: Padre, Abuela)</label>
                    <input type="text" id="parentesco" name="parentesco" 
                           value="<?php echo htmlspecialchars($datos_adulto['parentesco_familiar']); ?>" required>
                </div>
                
                <button type="submit" class="boton-primario">Guardar Cambios del Paciente</button>
            </form>
        </div>
    </main>
</body>
</html>