<?php

namespace Goteo\Model {
	
	use Goteo\Core\Redirection;

	class User extends \Goteo\Core\Model {
		
        public
            // Profile data
            $id = false,
            $email,
            $name,  // nombre completo
            $avatar = 'no-avatar.jpg', //imagen
            $about,  // texto: que nos puede contar
            $interests, // ya aclararemos esto @TODO
            $contribution,  // texto: que puede aportar a Goteo
            $blog,
            $twitter,
            $facebook,
            $linkedin,
            $country,
            $worth;  // total de contribución

	    public function __set($name, $value) {
            $this->$name = $value;
        }
			
        /**
         * Guardar usuario.
         * Guarda los valores de la instancia del usuario en la tabla.
         * 
         * @TODO: Revisar.
         * 
         * Reglas:
         *  - id *
         *  - email *
         *  - password
         * 
         * @param array $errors     Errores devueltos pasados por referencia.
         * @return bool true|false
         */
        public function save(&$errors = array()) {
            if($this->validate($errors)) {
                $data[':id'] = self::idealiza($this->id);
                if(!empty($this->name)) {
                    $data[':name'] = $this->name;
                }
                else {
                    $data[':name'] = $this->id;
                }
                if(!empty($this->email)) {
                    $data[':email'] = $this->email;
                }
                if(!empty($this->password)) {
                    $data[':password'] = sha1($this->password);
                }
                $data[':signup'] = 'CURRENT_TIMESTAMP';
                $data[':active'] = true;
                return self::query("
                    REPLACE INTO user (
                        id,
                        name,
                        email,
                        password,
                        signup,
                        active
                     )
                     VALUES (
                        :id,
                        :name,
                        :email,
                        :password,
                        :signup,
                        :active
                     )",
                $data);
            }
            return false;
        }
        
        /**
         * Validación de datos de usuario.
         * 
         * @param array $errors     Errores devueltos pasados por referencia.
         * @return bool true|false
         */
        public function validate(&$errors = array()) {
            // Nombre de usuario (id)
            if(!empty($this->id)) {
                $id = self::idealiza($this->id);
                $query = self::query('SELECT id FROM user WHERE id = ?', array($id));
                if($query->fetchColumn()) {
                    $errors['username'] = 'El usuario ya existe.';
                }
            }
            else {
                $errors['username'] = 'El nombre de usuario usuario es obligatorio.';
            }                
            // E-mail
            if(!empty($this->email)) {
                $query = self::query('SELECT email FROM user WHERE email = ?', array($this->email));
                if($query->fetchObject()) {
                    $errors['email'] = 'El dirección de correo ya corresponde a un usuario registrado.';
                }
            }
            else {
                $errors['email'] = 'La dirección de correo es obligatoria.';
            }            
            // Contraseña
            if(!empty($this->password)) {
                if(strlen($this->password)<8) {
                    $errors['password'] = 'La contraseña debe contener un mínimo de 8 caracteres.';
                }
            }
            else {
                $errors['password'] = 'La contraseña no puede estar vacía.';
            }            
            return empty($errors);
        }
        
        /**
         * Usuario.
         * 
         * @param string $id    Nombre de usuario
         * @return obj|false    Objeto de usuario, en caso contrario devolverá 'false'.
         */
        public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM user WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                return false;
            }
        }
        
        /**
         * Lista de usuarios.
         * 
         * @param  bool $visible    true|false
         * @return mixed            Array de objetos de usuario activos|todos.
         */
        public static function getAll($visible = true) {
            $query = self::query("SELECT * FROM user WHERE active = ?", array($visible));
            return $query->fetchAll(__CLASS__);          
        }

		/**
		 * Validación de usuario.
		 *  
		 * @param string $username Nombre de usuario
		 * @param string $password Contraseña
		 * @return obj|false Objeto del usuario, en caso contrario devolverá 'false'.
		 */
		public static function login($username, $password) {
			$query = self::query("
				SELECT
					id
				FROM user
				WHERE BINARY id = :username
				AND BINARY password = :password",
				array(
					':username' => trim($username),
					':password' => sha1($password)
				)
			);
			return $query->fetchObject(__CLASS__);
		}
		
		/**
		 * Comprueba si el usuario está identificado.
		 * 
		 * @return boolean
		 */
		public static function isLogged() {
			return !empty($_SESSION['user']);
		}
		
		/**
		 * Restringe el acceso sólo a usuarios identificados.
		 * En caso de que no esté identificado lo redirecciona al login.
		 */
		public static function restrict() {
			if(!static::isLogged()) {
				throw new Redirection("/user/login");
			}
		}
	}   
}