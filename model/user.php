<?php

namespace Goteo\Model {

	class User extends \Goteo\Core\Model {
		
		public 
			// Profile data
			$id = false,
            $user,  //nombre de usuario que aparece en sus mensajes
			$email,
			$name,  // nombre completo
			$avatar, //imagen
			$about,  // texto: que nos puede contar
			$interests = array(),
			$contribution,  // texto: que puede aportar a Goteo
			$blog,
			$twitter,
			$facebook,
			$linkedIn,
			$country,
			$worth;  // total de contribución

		/*
		 *  Cargamos los datos del usuario al crear la instancia
		 */
		public function __construct($id = null) {
			if ($id != null) {
				$fields = self::get($id);

				foreach ($fields as $data=>$value) {
					if (isset($this->$data)) {
						$this->$data = $value;
					}
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
			if (!empty($exist->user)) {
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
		public function save ($data) {
			if (!is_array($data) ||
				empty($data['user']) ||
				empty($data['email']) ||
				empty($data['pass'])) {
					return false;
				}

			// si cambian el usuario o el email tiene que verificar que no exista 

			// si cambian el nombre de usuario seria ideal que cambiaramos el id en todas las tablas relacionadas... currazo

			// si ponen un nuevo email tiene que corresponderse la comprobación
			//  y no deberia cambiar hasta recibir el email de confirmación


			// si ponen una nueva contraseña, debe corresponderse con la comprobacion
			//  y sobretodo , tiene que ser correcta la antigua

		}
	}   
}