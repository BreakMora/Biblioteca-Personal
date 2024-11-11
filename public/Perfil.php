<?php

    session_start();
    require_once '../src/config/Config.php';

    if (!isset($_SESSION['user'])) {
        header('Location: Login.php');
        exit();
    }

    $google_id = $_SESSION['user']['id'];
    $stmt = $mysqli->prepare("SELECT id, nombre FROM usuarios WHERE google_id = ?");
    $stmt->bind_param("s", $google_id);
    $stmt->execute();   
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $id = $user['id'];
        $nombre = $user['nombre'];
    } else {
        echo "Error: Usuario no encontrado.";
        exit();
    }

    // Consulta para obtener los libros asociados al usuario autenticado
    $stmtLibros = $mysqli->prepare("SELECT google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado FROM libros_guardados WHERE user_id = ?");
    $stmtLibros->bind_param("i", $id);
    $stmtLibros->execute();
    $resultLibros = $stmtLibros->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espacio Personal</title>
</head>
<body>

    <h3>Inicio de sesión exitoso. Bienvenido, <?php echo htmlspecialchars($nombre) . '!'; ?></h3>
    <br><a href="Libros.php">Buscar Libros</a>
    <br><a href="Logout.php">Cerrar sesión</a>

    <h4>Tu Biblioteca Personal</h4>
    <?php if ($resultLibros->num_rows > 0): ?>
        <ul>
            <?php while ($libro = $resultLibros->fetch_assoc()): ?>
                <li>
                    <h5><?php echo htmlspecialchars($libro['titulo']); ?></h5>
                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
                    <p><strong>Fecha de guardado:</strong> <?php echo htmlspecialchars($libro['fecha_guardado']); ?></p>
                    <?php if ($libro['imagen_portada']): ?>
                        <img src="<?php echo htmlspecialchars($libro['imagen_portada']); ?>" alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>" style="max-width: 150px;">
                    <?php endif; ?>
                    <p><strong>Reseña Personal:</strong> <?php echo htmlspecialchars($libro['reseña_personal']); ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No tienes libros guardados en tu biblioteca personal.</p>
    <?php endif; ?>


</body>
</html>