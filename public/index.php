<?php

    session_start();

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

        $resultado = $libroController->eliminar_Libro($conn, $libro_id, $id); // Llamamos al controlador para eliminar el libro

        if($resultado){
            ?>
            <script>
                window.alert("Libro eliminado de tu biblioteca.");
            </script>
            <?php
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
    <link rel="stylesheet" href="http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/public/assets/Perfil.css">
</head>
<body>

    <header class="contenedor-navegacion">
        <nav>
            <a class="btn" href="Logout.php">Cerrar sesión</a>
            <a class="btn" href="Libros.php">Buscar Libros</a>
        </nav>
    </header>

    <h3><br>Bienvenido, <?php echo htmlspecialchars($nombre) . '!'; ?></h3>
    <h3>Biblioteca Personal</h3>
    
    <section class="contenedor-libros-guardados">
        <?php if ($resultLibros->num_rows > 0): ?>
                <?php while ($libro = $resultLibros->fetch_assoc()): ?>
                    <div class="tarjeta-libro">
                        <?php if ($libro['imagen_portada']): ?>
                            <img src="<?php echo htmlspecialchars($libro['imagen_portada']); ?>" alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>" class="portada-libro">
                        <?php endif; ?>

                        <div class="informacion-libro">
                            <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
                            <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
                            <p><strong>Fecha de guardado:</strong> <?php echo htmlspecialchars($libro['fecha_guardado']); ?></p>
                            <p><strong>Reseña Personal:</strong> <?php echo htmlspecialchars($libro['reseña_personal']); ?></p>
                        </div>
                        
                        <!-- Formulario para borrar libro de la biblioteca -->
                        <form method="POST" action="Perfil.php" class="formulario-libro">
                            <input type="hidden" name="libro_id" value="<?php echo htmlspecialchars($libro['id']); ?>">
                            <button type="submit" class="btn-eliminar">Borrar de mi biblioteca</button>
                        </form>
                    </div>
                <?php endwhile; ?>
        <?php else: ?>
            <p>No tienes libros guardados en tu biblioteca personal.</p>
        <?php endif; ?>
    </section>
    
</body>
</html>
