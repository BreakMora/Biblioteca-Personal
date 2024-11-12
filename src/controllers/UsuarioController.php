<?php

require_once (__DIR__ . '/../models/Usuario.php'); // Incluimos models de Usuario

    class UsuarioController {
        private $usuario;

        public function __construct($conn) {
            $this->usuario = new Usuario($conn); // Crear una instancia del modelo Usuario
        }

        // Verificar si el usuario existe
        public function verificar_Usuario($google_id){
            return $this->usuario->verificar_Usuario($google_id); // Llama al metodo Verificar_Usuario del modelo Usuario
        }

         // Guarda un nuevo usuario si no existe en la base de datos
        public function guardar_Usuario($email, $nombre, $google_id){
            return $this->usuario->guardar_Usuario($email, $nombre, $google_id);  // Llama al método guardar_Usuario del modelo Usuario
        }

        // Obtener el ID del usuario
        public function get_Idusuario($google_id) {
            return $this->usuario->get_Id_Usuario($google_id); // Llama al método get_Id_Usuario del modelo Usuario
        }

        // Obtener el datos del usuario
        public function get_DatosUsuario($google_id) {
            return $this->usuario->get_DatosUsuario($google_id); // Llama al método get_Id_Usuario del modelo Usuario
        }
    }

?>