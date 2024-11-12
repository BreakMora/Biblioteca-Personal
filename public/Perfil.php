<?php
    require_once '../src/config/Config.php';
    require_once '../src/controllers/LibroController.php';+
    require_once '../src/controllers/UsuarioController.php';

    if (!isset($_SESSION['user'])) {
        header('Location: Login.php');
        exit();
    }

    $usuarioController = new UsuarioController($conn);
    // Obtener datos del usuario
    $google_id = $_SESSION['user']['id'];
    $resultUsuario = $usuarioController->get_DatosUsuario($google_id);

    if($resultUsuario->num_rows > 0){
        $user = $resultUsuario->fetch_assoc();
        $id = $user['id'];
        $nombre = $user['nombre'];
    }

    // Crear una instancia del controlador de libros
    $libroController = new LibroController();

    // Se recibe una solicitud para eliminar un libro
    if (isset($_POST['libro_id'])) {
        $libro_id = (int) $_POST['libro_id'];

        if ($libro_id > 0) {
            $libroController->eliminar_Libro($conn, $libro_id, $id); // Llamamos al controlador para eliminar el libro
            header('Location: Perfil.php'); // Regresar al perfil después de eliminar
            exit();
        }
    }
    //Obtener los libros asociados al usuario autenticado
    $resultLibros = $libroController->obtenerLibros($conn, $id);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espacio Personal</title>
    <link rel="stylesheet" href="styles.css"> <!-- Agregar tu archivo de estilos -->
</head>
<body>

    <div class="header">
        <a href="Logout.php">Cerrar sesión</a>
        <a href="Libros.php">Buscar Libros</a>
        <a href="Perfil.php">Perfil</a>
    </div>

    <h1>Bienvenido a tu espacio personal, <?php echo htmlspecialchars($nombre) . '!'; ?></h1>

    <h2>Tu Biblioteca Personal</h2>

    <?php if ($resultLibros->num_rows > 0): ?>
        <div class="libros-container">
            <?php while ($libro = $resultLibros->fetch_assoc()): ?>
                <div class="libro">
                    <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
                    <p><strong>Fecha de guardado:</strong> <?php echo htmlspecialchars($libro['fecha_guardado']); ?></p>
                    
                    <?php if ($libro['imagen_portada']): ?>
                        <!-- Mostrar la imagen solo si hay una URL válida en el campo imagen_portada -->
                        <img src="<?php echo htmlspecialchars($libro['imagen_portada']); ?>" alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>" class="libro-imagen">
                    <?php else: ?>
                        <p>No hay imagen disponible.</p>
                    <?php endif; ?>

                    <p><strong>Reseña Personal:</strong> <?php echo htmlspecialchars($libro['reseña_personal']); ?></p>

                    <!-- Formulario para borrar libro de la biblioteca -->
                    <form method="POST" action="Perfil.php">
                        <input type="hidden" name="libro_id" value="<?php echo htmlspecialchars($libro['id']); ?>">
                        <button type="submit" class="btn-eliminar">Borrar de mi biblioteca</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No tienes libros guardados en tu biblioteca personal.</p>
    <?php endif; ?>

</body>
</html>
