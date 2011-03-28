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
<<<<<<< HEAD
		public function save ($data, &$errors = array()) {
			if (!is_array($data)) {
					$errors[] = 'Datos insuficientes';
					return false;
				}

			// si cambian el usuario o el email tiene que verificar que no exista
			if (!empty($data['nuser'])) {
				$ok_nuser = true;
				if (strcmp($data['nuser'], $this->user) !== 0) {
					$query = self::query("SELECT user FROM user WHERE user = :user", array(':user' => $data['nuser']));
					$exist = $query->fetchObject();
					if (!empty($exist->user)) {
						$errors[] = 'El usuario ya existe';
						$ok_nuser = false;
					}
				}
				else {
					// no han cambiado el usuario
					$errors[] = 'El nuevo nombre de usuario deberia ser diferente al actual';
					$ok_nuser = false;
				}
			}
			
			if (!empty($data['nemail'])) {
				$ok_nemail = true;
				if (strcmp($data['nemail'], $this->email) !== 0) {
					$query = self::query("SELECT email FROM user WHERE email = :email", array(':email' => $data['nemail']));
					$exist = $query->fetchObject();
					if (!empty($exist->email)) {
						$errors[] = 'El email ya corresponde a un usuario registrado';
						$ok_nemail = false;
					}
				}
				else {
					// no han cambiado el email
					$errors[] = 'El nuevo email deberia ser diferente al actual';
					$ok_nemail = false;
				}

				// si ponen un nuevo email tiene que corresponderse la comprobación
				if (strcmp($data['nemail'], $data['cemail']) !== 0) {
					$errors[] = 'La comprobación de email no coincide';
					$ok_nemail = false;
				}
				// @TODO  y no deberia cambiar hasta recibir el email de confirmación
			}

			// si ponen una nueva contraseña, debe corresponderse con la comprobacion
			if (!empty($data['npass'])) {
				$ok_npass = true;
				// tiene que ser correcta la antigua
				$query = self::query("SELECT id FROM user WHERE BINARY user = :user AND BINARY password = :pass", array(':user' => $this->user, ':pass' => md5($data['pass'])));
				$exist = $query->fetchObject();
				if (!$exist->id) {
					$errors[] = 'La contraseña antigua no es correcta';
					$ok_npass = false;
				}
				
				if (strcmp(md5($data['npass']), $this->pass) !== 0) {
					if (strcmp($data['npass'], $data['cpass']) !== 0) {
						$errors[] = 'La comprobación de contraseña no coincide';
						$ok_npass = false;
					}
				}
				else {
					//no ha cambiado la contraseña
					$errors[] = 'La contraseña nueva deberia ser diferente a la actual';
					$ok_npass = false;
				}
			}

			// @TODO si cambian el nombre de usuario seria ideal que cambiáramos
			//  el id en todas las tablas relacionadas... @currazo
			//	':id'	=> self::idealiza($data['user']),
			
			if (!empty($errors)) {
				return false;
			} else {

				$set = '';
				$values = array();

				if ($ok_nuser) {
					$set .= "user = :user, ";
					$values[':user'] = $data['nuser'];
					// @TODO además deberia enviar un email para avisar
				}
				if ($ok_nemail) {
					$set .= "email = :email, ";
					$values[':email'] = $data['nemail'];
					// @TODO además deberia enviar un email para avisar
				}
				if ($ok_npass) {
					$set .= "pass = :pass, ";
					$values[':pass'] = md5($data['npass']);
					// @TODO además deberia enviar un email para avisar
				}

				if (!empty($values)) {
					$set .= "lastedit = :lastedit";
					$values[':lastedit'] = date('Y-m-d');
					$values[':id'] = $this->id;

					$sql = "UPDATE user SET " . $set . " WHERE id = :id";
					if (self::query($sql, $values)) {
						$this->load();
						return true;
					} else {
						echo "ERROR $sql<br />Al actualizar los datos<pre>" . print_r($values, 1) . "</pre>";
						return false;
					}
				}
				else {
					// nada nuevo bajo el sol
//					$errors[] = 'No hay ningún cambio que guardar';
					return true;
				}

			}


=======
		public static function isLogged() {
			return !empty($_SESSION['user']);
>>>>>>> Users
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