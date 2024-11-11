<?php

require_once(__DIR__ . '/../config/Config.php');  // Asegúrate de que la conexión a la base de datos esté incluida.

class LibroController {

    // Método para agregar un libro a la biblioteca personal del usuario
    public function agregarLibro($googleBooksId, $titulo, $autor, $imagen, $reseña, $user_id) {
        global $mysqli;  // Accedemos a la conexión a la base de datos desde el archivo de configuración

        // Insertar el libro en la base de datos
        $stmt = $mysqli->prepare("INSERT INTO libros_guardados (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $googleBooksId, $titulo, $autor, $imagen, $reseña);

        // Ejecutar la consulta y verificar el resultado
        if ($stmt->execute()) {
            return true;  // Libro agregado con éxito
        } else {
            return false;  // Error al agregar el libro
        }
    }
}