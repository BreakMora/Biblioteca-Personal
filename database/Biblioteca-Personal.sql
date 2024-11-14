CREATE DATABASE biblioteca;

USE biblioteca;

CREATE TABLE usuarios ( 
	id INT AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(200) NOT NULL UNIQUE,
	nombre VARCHAR(100) NOT NULL,
	google_id VARCHAR(275) NOT NULL UNIQUE,
	fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE libros_guardados (
    	id INT AUTO_INCREMENT PRIMARY KEY,
    	user_id INT NOT NULL,
    	google_books_id VARCHAR(275) NOT NULL,
    	titulo VARCHAR(275) NOT NULL,
    	autor VARCHAR(200),
    	imagen_portada VARCHAR(300),
    	reseña_personal TEXT,
    	fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    	FOREIGN KEY (user_id) REFERENCES usuarios(id)
);

SELECT * FROM usuarios;

SELECT * FROM libros_guardados;