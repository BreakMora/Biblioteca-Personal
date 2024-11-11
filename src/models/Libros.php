<?php

    class Libros {
        public $google_books_id;
        public $titulo;
        public $autores;
        public $editorial;
        public $descripcion;
        public $imagen;

        public function __construct($google_books_id, $titulo, $autores, $editorial, $descripcion, $imagen) {
            $this->google_books_id = $google_books_id;
            $this->titulo = $titulo;
            $this->autores = $autores;
            $this->editorial = $editorial;
            $this->descripcion = $descripcion;
            $this->imagen = $imagen;
        }

        public static function respuestaGoogleBooks($googleBook) {
            $google_books_id = $googleBook['id'] ?? 'No Google Books ID available';
            $titulo = $googleBook['volumeInfo']['title'] ?? 'No title available';
            $autores = $googleBook['volumeInfo']['authors'] ?? ['No authors available'];
            $editorial = $googleBook['volumeInfo']['publisher'] ?? 'No publisher available';
            $descripcion = $googleBook['volumeInfo']['description'] ?? 'No description available';
            $imagen = $googleBook['volumeInfo']['imageLinks']['thumbnail'] ?? 'https://via.placeholder.com/128x200';

            return new self($google_books_id, $titulo, $autores, $editorial, $descripcion, $imagen);
        }
    }
    
?>