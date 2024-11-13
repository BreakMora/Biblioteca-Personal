<?php
    require_once (__DIR__ . '/../config/Config.php');

    class Libros {
        // Propiedades del libro
        private $google_books_id;
        private $titulo;
        private $autores;
        private $editorial;
        private $descripcion;
        private $imagen;
        private $resena;  // Propiedad para la reseña
        private $conn; // Conexión a la base de datos

        // Constructor
        public function __construct($conn, $google_books_id, $titulo, $autores, $editorial, $descripcion, $imagen, $resena) {
            $this->conn = $conn;  // Conexión a la base de datos
            $this->google_books_id = $google_books_id;
            $this->titulo = $titulo;
            $this->autores = $autores;
            $this->editorial = $editorial;
            $this->descripcion = $descripcion;
            $this->imagen = $imagen;
            $this->resena = $resena; // Asignación de la reseña
        }

        // Método que procesa la respuesta del API de Google Books
        public static function respuesta_GoogleBooks($googleBook) {
            $google_books_id = $googleBook['id'] ?? 'No hay ID de Google Books disponible para el Libro';
            $titulo = $googleBook['volumeInfo']['title'] ?? 'No hay titulo disponible para el Libro';
            $autores = isset($googleBook['volumeInfo']['authors']) ? implode(", ", $googleBook['volumeInfo']['authors']) : 'No hay autor disponible para el Libro';
            $editorial = $googleBook['volumeInfo']['publisher'] ?? 'No hay editorial disponible para el Libro';
            $descripcion = $googleBook['volumeInfo']['description'] ?? 'No hay descripcion disponible para el Libro';
            $imagen = $googleBook['volumeInfo']['imageLinks']['thumbnail'] ?? 'https://via.placeholder.com/128x200';
            $resena = '';  // La reseña estará vacía inicialmente, ya que no se recibe de la API

            return new self(null, $google_books_id, $titulo, $autores, $editorial, $descripcion, $imagen, $resena);
        }

        // Método que guarda el libro en la base de datos
        public function guardar_Libro($user_id) {
            // Verificar si el libro ya está guardado en la base de datos
            $stmt = $this->conn->prepare("SELECT id FROM libros_guardados WHERE user_id = ? AND google_books_id = ?");
            $stmt->bind_param("is", $user_id, $this->google_books_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) { // El libro ya está guardado
                return false;  // No agregarlo nuevamente
            }

            // Si el libro no existe, se inserta en la base de datos
            $stmt = $this->conn->prepare("INSERT INTO libros_guardados (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $this->google_books_id, $this->titulo, $this->autores, $this->imagen, $this->resena);

            return $stmt->execute(); // Ejecutar la consulta y retorna true si se realizó con éxito.
        }
        
        // Método que elimina un libro de la base de datos
        public static function eliminar_Libro($conn, $libro_id, $user_id) {
            $stmt = $conn->prepare("DELETE FROM libros_guardados WHERE id = ? AND user_id = ?"); // Verificamos si el libro pertenece al usuario antes de eliminarlo
            $stmt->bind_param("ii", $libro_id, $user_id);

            return $stmt->execute();  // Ejecuta la consulta y retorna true si se realizo con exito.
        }

        public static function limitarDescripcion($descripcion, $limitePalabras = 30) {
            $palabras = explode(' ', $descripcion);
            if (count($palabras) > $limitePalabras) {
                $descripcionCorta = implode(' ', array_slice($palabras, 0, $limitePalabras)) . '...';
                return $descripcionCorta;
            }
            return $descripcion; //retorna la descripcion resumida
        }

        // Método para obtener los libros guardados por el usuario
        public static function libroGuardado($conn, $user_id) {
            $stmt = $conn->prepare("SELECT id, google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado FROM libros_guardados WHERE user_id = ?"); // Consultamos la BD para obtener los libros guardados por el usuario
            $stmt->bind_param("i", $user_id);  // Vinculamos el parámetro con el ID del usuario
            $stmt->execute();
            
            return $stmt->get_result();  // Retornamos el resultado de la consulta
        }

        // Getters los datos del libro
        public function getGoogleBooksId() {
            return $this->google_books_id;
        }

        public function getTitulo() {
            return $this->titulo;
        }

        public function getAutores() {
            return $this->autores;
        }

        public function getEditorial() {
            return $this->editorial;
        }

        public function getDescripcion() {
            return $this->descripcion;
        }

        public function setDescripcion($descripcion) { // Asigna la descripcion resumida
            $this->descripcion = $descripcion;
        }

        public function getImagen() {
            return $this->imagen;
        }

        public function getResena() {
            return $this->resena; // Getter para la reseña
        }
    }
    
?>