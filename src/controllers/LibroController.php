<?php

require_once (__DIR__ . '/../models/Libro.php');
require_once (__DIR__ . '/Google_BooksAPI.php');

class LibroController {

    public function __construct() {
    
    }

    // Método para buscar libros en Google Books API
    public function buscar_Libros($query) {
        $googleBooksAPI = new GoogleBooksAPI();
        $libros_data = $googleBooksAPI->buscar_Libros($query);

        $libros = [];
        if ($libros_data) {
            foreach ($libros_data as $libro) {
                // Se asegura de llamar a la respuesta de Google Books correctamente
                $libros[] = Libros::respuesta_GoogleBooks($libro);
            }
        }
        return $libros;
    }

    // Método para agregar un libro a la biblioteca personal del usuario
    public function agregar_Libro($conn, $googleBooksId, $titulo, $autor, $imagen, $resena, $user_id) {
        // Se pasa el parámetro $conn correctamente y se crea la instancia de Libros
        $libro = new Libros($conn, $googleBooksId, $titulo, $autor, null, null, $imagen, $resena);
        
        // Se envía el objeto al modelo para su inserción a la Base de Datos
        return $libro->guardar_Libro($user_id);
    }

    // Método para eliminar un libro de la biblioteca personal del usuario
    public function eliminar_Libro($conn, $libro_id, $user_id) {
        // Uso el método del modelo para eliminar el libro
        return Libros::eliminar_Libro($conn, $libro_id, $user_id);
    }

    // Método para obtener los libros del usuario
    public function obtenerLibros($conn, $user_id) {
        // Consultar libros de la base de datos para un usuario específico
        $stmt = $conn->prepare("SELECT id, google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado FROM libros_guardados WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}