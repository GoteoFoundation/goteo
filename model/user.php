<?php
//echo '<pre>' . print_r($fields, 1) . '</pre>';

namespace Goteo\Model {

	class User extends \Goteo\Core\Model {
		
		public 
			// Profile data
			$id = false,
            $user,  //nombre de usuario que aparece en sus mensajes
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

		/*
		 *  Cargamos los datos del usuario al crear la instancia
		 */
		public function __construct($id = null) {
			if ($id != null) {
				$this->id = $id;
				$this->load();
			}
		}

		private function load() {
			$fields = self::get($this->id);

			foreach ($fields as $data=>$value) {
				if (property_exists($this, $data) && !empty($value)) {
					$this->$data = $value;
				}
			}
		}

		public static function validate ($user, $pass) {
			if (empty($user) || empty($pass))
				return false;

			$query = self::query("SELECT id FROM user WHERE BINARY user = :user AND BINARY password = :pass", array(':user' => $user, ':pass' => md5($pass)));
			$exist = $query->fetchObject();
			if ($exist->id) {
				return $exist->id;
			} else {
				return false;
			}
		}

		/*
		 *  Metodo para verificar los datos del usuario que se quiere registrar
		 *
		 *  @FIXME si se parte en cachos se podrá reutilizar en el metodo save
		 */
		public static function check($data, &$errors = array()) {
			echo 'Verificando <pre>' . print_r($data, 1) . '</pre>';
			if (!is_array($data) ||
				empty($data['user']) ||
				empty($data['email']) ||
				empty($data['pass'])) {
					$errors[] = 'Faltan valores obligatorios';
					return false;
				}

			// mirar si el nombre de usuario ya está cogido
			$query = self::query("SELECT user FROM user WHERE user = :user", array(':user' => $data['user']));
			$exist = $query->fetchObject();
			if (!empty($exist->user)) {
				$errors[] = 'El usuario ya existe';
			}

			// mirar si el email esta registrado
			$query = self::query("SELECT email FROM user WHERE email = :email", array(':email' => $data['email']));
			$exist = $query->fetchObject();
			if (!empty($exist->email)) {
				$errors[] = 'El email ya corresponde a un usuario registrado';
			}

			// mirar si la comprobación de email coincide
			if (strcmp($data['email'], $data['cemail']) !== 0) {
				$errors[] = 'La comprobación de email no coincide';
			}
				
			// mirar si la comprobación de contraseña coincide
			if (strcmp($data['pass'], $data['cpass']) !== 0) {
				$errors[] = 'La comprobación de contraseña no coincide';
			}

			if (!empty($errors))
				return false;
			else
				return true;
		}


		/*
		 *   Metodo para dar de alta un nuevo usuario
		 *
		 *   Datos para darlo de alta son los mínimos requeridos:
		 *		user = nombre de usuario
		 *		email = email
		 *		pass = contraseña
		 */
		public function create($data = array()) {
			if (!is_array($data) || 
				empty($data['user']) ||
				empty($data['email']) ||
				empty($data['pass'])) {
					return false;
				}

			$values = array(
				':id'	=> self::idealiza($data['user']),
				':user' => $data['user'],
				':name'	=> $data['user'],
				':email' => $data['email'],
				':password' => md5($data['pass']),
				':signup'	=> date('Y-m-d'),
				':active'	=> 1
				);

			$sql = "INSERT INTO user (id, user, name, email, password, signup, active)
				 VALUES (:id, :user, :name, :email, :password, :signup, :active)";
			if (self::query($sql, $values)) {
				$this->id = $values[':id'];
				return true;
			} else {
				echo "ERROR $sql<br /><pre>" . print_r($values, 1) . "</pre>";
				return false;
			}
		}


		/**
		 * @FIXME: Devuelve el usuario como un array, utiliza parámetros con nombre (a modo de ejemplo).
		 * @param string $id
		 */
		public static function get ($id) {            
			$query = self::query("SELECT * FROM user WHERE id = :id", array(':id' => $id));
			return $query->fetchObject();
		}

		/**
		 * @FIXME: Devuelve todos los usuarios activos en un array de arrays, utiliza parámetros con signo de interrogación (a modo de ejemplo)
		 * @TODO: La he llamado 'getAll', pero podría ser también 'all' o 'getList'. En un principio pensé en 'list', pero está reservada :(
		 */
		public static function getAll() {
			$query = self::query("SELECT * FROM user WHERE active = ?", array(true));
			return $query->fetchAll();        	
		}

		/**
		 *  Este metodo solo guarda los datos sensibles
		 *		nombre de usuario (campo `user`)
		 *		email
		 *		contraseña
		 *
		 * , para guardar la información del usuario usar el método update
		 * 
		 */
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


		}

		/*
		 *  Método para grabar la información adicional de usuario
		 *	este metodo no trata la imagen de avatar, en el controlador se captura esa gestión hacia un modelo/libreria de imagenes o algo así
		 *  lo que si hace es guardar el nombre de la imagen en el campo ;)
		 *
		 *   para los datos sensibles usar el método save
		 */
		public function update ($data, $errors = array()) {
			if (!is_array($data)) {
					$errors[] = 'Datos insuficientes';
					return false;
				}

			$fields = array('name', 'about', 'avatar', 'contribution', 'blog', 'twitter', 'facebook', 'linkedin');
			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if (!empty($data[$field])) {
					$set .= "$field = :$field, ";
					$values[":$field"] = $data[$field];
				}
			}

			if (!empty($values)) {
				$set .= "lastedit = :lastedit";
				$values[':lastedit'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE user SET " . $set . " WHERE id = :id";
//				echo "QUERY: $sql<br />Datos<pre>" . print_r($values, 1) . "</pre>";
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
				$errors[] = 'No hay ningún cambio que guardar';
				return false;
			}
		}

	}   
}