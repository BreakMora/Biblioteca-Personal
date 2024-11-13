<?php

    require_once (__DIR__ . '/../src/controllers/UsuarioController.php');
    require_once (__DIR__ . '/../src/controllers/LibroController.php');
    require_once (__DIR__ . '/../src/config/Config.php');

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['user'])) {
        header('Location: Login.php');
        exit();
    }

    // Pasar conexión al controlador
    $usuarioController = new UsuarioController($conn);
    $libroController = new LibroController();

    // Pasamos la variable google_id a UsuarioController
    $google_id = $_SESSION['user']['id'];
    $user_id = $usuarioController->get_Idusuario($google_id); // se llama al metodo get_IdUsuario del controlador

    // Verificar si hay un término de búsqueda
    $libros = null;
    if (isset($_GET['search'])) {
        $query = htmlspecialchars($_GET['search']); // Para evitar XSS
        $libros = $libroController->buscar_Libros($query); // se llama al metodo buscar_Libros del controlador
    }

    // Si se ha enviado el formulario para agregar un libro
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['google_books_id'])) {
        $googleBooksId = htmlspecialchars($_POST['google_books_id']);
        $titulo = htmlspecialchars($_POST['titulo']);
        $autor = htmlspecialchars($_POST['autor']);
        $imagen = $_POST['imagen'];
        $reseña = htmlspecialchars($_POST['resena']);

        $resultado = $libroController->agregar_Libro($conn, $googleBooksId, $titulo, $autor, $imagen, $reseña, $user_id); // se envian los datos el metodo agregar_Libro para agregarlo

        if ($resultado) {
            echo "Libro agregado a tu biblioteca.";
        } else {
            echo "Error al agregar el libro.";
        }
    }

    // Limitar la descripción de cada libro
    if (isset($libros)) {
        foreach ($libros as $libro) {
            $libro->setDescripcion($libroController->limitarDescripcion($libro->getDescripcion()));
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Libros</title>
    <link rel="stylesheet" href="http://localhost/Parcial4_Desarrollo/Biblioteca-Personal/public/assets/busqueda.css">
</head>
<body>

    <header>
        <nav>
            <a href="Perfil.php">Volver</a>
        </nav>
    </header>

    <main>
        <section class="seccion-busqueda">
            <h1>Buscar Libros en Google Books</h1>
            <form method="GET" action="libros.php">
                <input type="text" name="search" placeholder="Buscar por título o autor" required>
                <button type="submit">Buscar</button>
            </form>
        </section>

        <?php if ($libros): ?>
            <section class="resultado-busqueda">
                <h2>Resultados de la búsqueda:</h2>
                <div class="contenedor-libros">
                    <?php foreach ($libros as $libro): ?>
                        <div class="tarjeta-libro">
                            <img src="<?php echo htmlspecialchars($libro->getImagen()); ?>" alt="imagen" class="portada-libro">
                            <div class="informacion-libro">
                                <h3 class="titulo-libro"><?php echo htmlspecialchars($libro->getTitulo()); ?></h3>
                                <p class="descripcion-libro"><strong>Autores:</strong> <?php echo htmlspecialchars($libro->getAutores()); ?></p>
                                <p class="descripcion-libro"><strong>Editorial:</strong> <?php echo htmlspecialchars($libro->getEditorial()); ?></p>
                                <p class="descripcion-libro"><strong>Descripción:</strong> <?php echo htmlspecialchars($libro->getDescripcion()); ?></p>

                                <!-- Formulario para agregar libro a la biblioteca personal -->
                                <form method="POST" action="libros.php">
                                    <input type="hidden" name="google_books_id" value="<?php echo htmlspecialchars($libro->getGoogleBooksId()); ?>">
                                    <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($libro->getTitulo()); ?>">
                                    <input type="hidden" name="autor" value="<?php echo htmlspecialchars($libro->getAutores()); ?>">
                                    <input type="hidden" name="imagen" value="<?php echo $libro->getImagen(); ?>">
                                    <textarea class="resena-usuario" name="resena" placeholder="Reseña personal"></textarea>
                                    <button type="submit">Agregar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php elseif (isset($query)): ?>
            <p>No se encontraron libros para la búsqueda "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </main>
    
</body>
</html>