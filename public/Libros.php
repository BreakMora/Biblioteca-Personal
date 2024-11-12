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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Libros</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <a href="Logout.php">Cerrar sesión</a>
    <a href="Perfil.php">Volver</a>

    <h1>Buscar Libros en Google Books</h1>

    <form method="GET" action="libros.php">
        <input type="text" name="search" placeholder="Buscar por título o autor" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($libros): ?>
        <h2>Resultados de la búsqueda:</h2>
        <div>
            <?php foreach ($libros as $libro): ?>
                <div class="libro">
                    <img src="<?php echo htmlspecialchars($libro->getImagen()); ?>" alt="imagen">
                    <h3><?php echo htmlspecialchars($libro->getTitulo()); ?></h3>
                    <p><strong>Autores:</strong> <?php echo htmlspecialchars($libro->getAutores()); ?></p>
                    <p><strong>Editorial:</strong> <?php echo htmlspecialchars($libro->getEditorial()); ?></p>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($libro->getDescripcion()); ?></p>

                    <!-- Formulario para agregar libro a la biblioteca personal -->
                    <form method="POST" action="libros.php">
                        <input type="hidden" name="google_books_id" value="<?php echo htmlspecialchars($libro->getGoogleBooksId()); ?>">
                        <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($libro->getTitulo()); ?>">
                        <input type="hidden" name="autor" value="<?php echo htmlspecialchars($libro->getAutores()); ?>">
                        <input type="hidden" name="imagen" value="<?php echo $libro->getImagen(); ?>">
                        <label for="resena">Escribe una reseña personal:</label>
                        <textarea name="resena" placeholder="Escribe una reseña personal"></textarea>
                        <button type="submit">Agregar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($query)): ?>
        <p>No se encontraron libros para la búsqueda "<?php echo htmlspecialchars($query); ?>"</p>
    <?php endif; ?>

</body>
</html>