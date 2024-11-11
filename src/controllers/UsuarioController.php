<?php

require_once '../config/Config.php';

    class UsuarioController {
        private $db;

        public function __construct($mysqli){
            $this->db = $mysqli;
        }

        public function Usuario_Existe($google_id){
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE google_id = ?");
            $stmt->bind_param("s", $google_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function guardarUsuario($email, $nombre, $google_id){
            if(!$this->Usuario_Existe($google_id)){
                $stmt = $this->db->prepare("INSERT INTO usuarios (email, nombre, google_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $email, $nombre, $google_id);
                if($stmt->execute()){
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }

    }

?>