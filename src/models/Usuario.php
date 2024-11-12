<?php

require_once (__DIR__ . '/../config/Config.php');

class Usuario {
    private $conn; // Conexión a la base de datos

    // Constructor
    public function __construct($conn){
        $this->conn = $conn;  // Asignamos la conexión a la propiedad $conn
    }

    // Verificar si el usuario existe en la base de datos
    public function verificar_Usuario($google_id){
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Guardar un nuevo usuario en la base de datos
    public function guardar_Usuario($email, $nombre, $google_id){
        if(!$this->verificar_Usuario($google_id)){
            $stmt = $this->conn->prepare("INSERT INTO usuarios (email, nombre, google_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $nombre, $google_id);
            if($stmt->execute()){
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // Obtener el ID de un usuario por su Google ID
    public function get_Id_Usuario($google_id) {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            return $user['id'];
        }
        return null;
    }

    // Obtener los datos del usuario por Google ID
    public function get_DatosUsuario($google_id) {
        $stmt = $this->conn->prepare("SELECT id, nombre FROM usuarios WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        return $stmt->get_result();
    }

}

?>