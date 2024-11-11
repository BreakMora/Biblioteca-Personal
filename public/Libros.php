<?php

require_once '../src/controllers/Google_BooksAPI.php';
require_once '../src/models/Libros.php';

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
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($query)): ?>
        <p>No se encontraron libros para la búsqueda "<?php echo htmlspecialchars($query); ?>"</p>
    <?php endif; ?>

</body>
</html>
