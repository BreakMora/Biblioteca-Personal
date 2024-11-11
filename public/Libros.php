<?php
    require_once '../src/controllers/LibroController.php'; //../src/controllers/LibroController.php
    require_once '../src/controllers/Google_BooksAPI.php';
    require_once '../src/models/Libros.php';
    require_once '../src/config/Config.php'; // Para la conexión a la base de datos

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['user'])) {
        header('Location: Login.php');
        exit();
    }

    // Recuperar el google_id del usuario en sesión
    $google_id = $_SESSION['user']['id'];

    // Obtener el id del usuario de la base de datos local
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE google_id = ?");
    $stmt->bind_param("s", $google_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El usuario existe, obtener su id local
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    } else {
        // Si no se encuentra el usuario, redirigir al login
        header('Location: Login.php');
        exit();
    }

    // Verificar si hay un término de búsqueda
    $books = null;
    if (isset($_GET['search'])) {
        $query = $_GET['search'];
        $controller = new GoogleBooksAPI();
        $books_data = $controller->buscarLibro($query);

        // Convertir los resultados de la API a objetos Book
        if ($books_data) {
            $books = [];
            foreach ($books_data as $book_data) {
                $books[] = Libros::respuestaGoogleBooks($book_data);
            }
        }
    }

    // Si se ha enviado el formulario para agregar un libro
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['google_books_id'])) {
        // Obtener los datos del libro y el ID del usuario autenticado
        $googleBooksId = $_POST['google_books_id'];
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $imagen = $_POST['imagen'];
        $reseña = $_POST['reseña'];

        // Crear una instancia del controlador y agregar el libro
        $libroController = new LibroController();
        $resultado = $libroController->agregarLibro($googleBooksId, $titulo, $autor, $imagen, $reseña, $user_id);

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

    <?php if ($books): ?>
        <h2>Resultados de la búsqueda:</h2>
        <div>
            <?php foreach ($books as $book): ?>
                <div class="book">
                    <img src="<?php echo $book->imagen; ?>" alt="imagen">
                    <h3><?php echo htmlspecialchars($book->titulo); ?></h3>
                    <p><strong>Autores:</strong> <?php echo implode(', ', $book->autores); ?></p>
                    <p><strong>Editorial:</strong> <?php echo htmlspecialchars($book->editorial); ?></p>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($book->descripcion); ?></p>

                    <!-- Formulario para agregar libro a la biblioteca personal -->
                    <form method="POST" action="libros.php">
                        <input type="hidden" name="google_books_id" value="<?php echo htmlspecialchars($book->google_books_id); ?>">
                        <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($book->titulo); ?>">
                        <input type="hidden" name="autor" value="<?php echo htmlspecialchars(implode(', ', $book->autores)); ?>">
                        <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($book->imagen); ?>">
                        <textarea name="reseña" placeholder="Escribe una reseña personal"></textarea>
                        <button type="submit">Agregar a mi biblioteca</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($query)): ?>
        <p>No se encontraron libros para la búsqueda "<?php echo htmlspecialchars($query); ?>"</p>
    <?php endif; ?>

</body>
</html>
