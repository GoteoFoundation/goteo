<?php

namespace Goteo\Model {

	use Goteo\Core\Redirection,
        Goteo\Library\Text;

	class User extends \Goteo\Core\Model {

        public
            $id = false,
            $role = null,
            $email,
            $name,
            $avatar,
            $about,
            $interests,
            $contribution,
            $keywords,
            $blog,
            $twitter,
            $facebook,
            $linkedin,
            $country,
            $worth;

	    public function __set($name, $value) {
            $this->$name = $value;
        }

        /**
         * Guardar usuario.
         * Guarda los valores de la instancia del usuario en la tabla.
         *
         * @TODO: Revisar. Esto solo sirve para registrar un nuevo usuario, no sirve para guardar datos...
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
                $data[':created'] = 'CURRENT_TIMESTAMP';
                $data[':active'] = true;
                return self::query("
                    REPLACE INTO user (
                        id,
                        name,
                        email,
                        password,
                        created,
                        active
                     )
                     VALUES (
                        :id,
                        :name,
                        :email,
                        :password,
                        :created,
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
                    $errors['username'] = Text::get('error register user exists');
                }
            }
            else {
                $errors['username'] = Text::get('error register username');
            }
            // E-mail
            if(!empty($this->email)) {
                $query = self::query('SELECT email FROM user WHERE email = ?', array($this->email));
                if($query->fetchObject()) {
                    $errors['email'] = Text::get('error register email exists');
                }
            }
            else {
                $errors['email'] = Text::get('error register email');
            }
            // Contraseña
            if(!empty($this->password)) {
                if(strlen($this->password)<8) {
                    $errors['password'] = Text::get('error register short password');
                }
            }
            else {
                $errors['password'] = Text::get('error register pasword');
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
//                        role_id AS role,
                $query = static::query("
                    SELECT
                        id,
                        name,
                        email,
                        password,
                        about,
                        keywords,
                        active AS visible,
                        avatar,
                        contribution,
                        twitter,
                        facebook,
                        linkedin,
                        worth,
                        created,
                        modified
                    FROM user
                    WHERE id = :id
                    ", array(':id' => $id));
                $user = $query->fetchObject(__CLASS__);

				// intereses (para proyectos es categoria(s) aunque los contenidos actuales son identicos no es el mismo concepto)
                $user->interests = User\Interest::get($id);

                // webs
                $user->webs = User\Web::get($id);

                return $user;

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
			if($row = $query->fetch()) {
			    return static::get($row['id']);
			}
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
		 * @deprecated
		 *
		 * Restringe el acceso sólo a usuarios identificados.
		 * En caso de que no esté identificado lo redirecciona al login.
		 */
		public static function restrict() {
			if(!static::isLogged()) {
				throw new Redirection('/user/login');
			}
		}

		public static function interests() {
            return array(
                1=>'Educación',
                2=>'Economía solidaria',
                3=>'Empresa abierta',
                4=>'Formación técnica',
                5=>'Desarrollo',
                6=>'Software',
                7=>'Hardware');
		}

        /**
         * Metodo para puntuar la informacuión del usuario al puntuar un proyecto
         * @param array $errors por referencia
         */
        public function check(&$errors = array()) {
            if (empty($this->name)) 
                $errors['name'] = Text::get('validate user field name');

            if (empty($this->avatar)) 
                $errors['avatar'] = Text::get('validate user field avatar');

            if (empty($this->about)) 
                $errors['about'] = Text::get('validate user field about');

            if (empty($this->interests)) 
                $errors['interests'] = Text::get('validate user field interests');

            $keywords = explode(',', $this->keywords);
            if ($keywords < 5) 
                $errors['keywords'] = Text::get('validate user field keywords');

            if (empty($this->contribution)) 
                $errors['contribution'] = Text::get('validate user field contribution');

            if (empty($this->webs))
                $errors['webs'] = Text::get('validate user field webs');

            if (empty($this->facebook)) 
                $errors['facebook'] = Text::get('validate user field facebook');

            return true;
        }

        /**
         * Metodo para guardar la información del usuario desde el primer paso del formulario de proyecto
         * @param array $errors por referencia
         */
        public function saveInfo(&$errors = array()) {

            $fields = array(
                'name',
                'avatar',
                'about',
                'keywords',
                'contribution',
                'twitter',
                'facebook',
                'linkedin'
            );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ', ';
                $set .= "$field = :$field";
                $values[":$field"] = $this->$field;
            }

			try {
				$values[':id'] = $this->id;

				$sql = "UPDATE user SET " . $set . " WHERE id = :id";
				self::query($sql, $values);

			} catch(\PDOException $e) {
                $errors[] = "Fallo al actualizar la información del usuario. " . $e->getMessage();
                return false;
			}

        }

	}
    
}