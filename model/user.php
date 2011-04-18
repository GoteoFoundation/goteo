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
            $contribution,
            $keywords,
            $active,
            $facebook,
            $twitter,
            $linkedin,
            $country,
            $worth,
            $created,
            $modified,
            $interests,
            $webs;

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
                // Nuevo usuario.
                if(empty($this->id)) {
                    $this->id = static::idealiza($this->name);
                    $data[':role_id'] = 3; // @FIXME: Provisionalmente: 3 = Usuario
                    $data[':created'] = 'CURRENT_TIMESTAMP';
                    $data[':active'] = false; // @TODO: Requiere activación.
                }
                $data[':id'] = $this->id;

                if(!empty($this->name)) {
                    $data[':name'] = $this->name;
                }

                if(!empty($this->email)) {
                    $data[':email'] = $this->email;
                }

                if(!empty($this->password)) {
                    $data[':password'] = sha1($this->password);
                }

                // @TODO: tratar la imagen y ponerla en la propiedad avatar (__FILES__?)
                if(!empty($this->avatar)) {
                    $data[':avatar'] = $this->avatar;
                }

                if(!empty($this->about)) {
                    $data[':about'] = $this->about;
                }

                if(!empty($this->keywords)) {
                    $data[':keywords'] = $this->keywords;
                }

                if(!empty($this->contribution)) {
                    $data[':contribution'] = $this->contribution;
                }

                if(!empty($this->facebook)) {
                    $data[':facebook'] = $this->facebook;
                }

                if(!empty($this->twitter)) {
                    $data[':twitter'] = $this->twitter;
                }

                if(!empty($this->linkedin)) {
                    $data[':linkedin'] = $this->linkedin;
                }

                if(!empty($this->interests)) {
                    $interests = User\Interest::get($this->id);
                    foreach($this->interests as $interest) {
                        if(!in_array($interest, $interests)) {
                            $_interest = new Model\User\Interest();
                            $_interest->id = $interest;
                            $_interest->user = $this->id;
                            $_interest->save($errors);
                            $interests[] = $_interest;
                        }
                    }
                    foreach($interests as $key => $interest) {
                        if(!in_array($interest, $this->interests)) {
                            $_interest = new Model\User\Interest();
                            $_interest->id = $interest;
                            $_interest->user = $this->id;
                            if ($interest->remove($errors)) {
                                unset($interests[$key]);
                            }
                        }
                    }
                }

                try {
                    // Construye SQL.
                    $query = "REPLACE INTO user (";
                    foreach($data AS $key => $row) {
                        $query .= substr($key, 1) . ", ";
                    }
                    $query = substr($query, 0, -2) . ") VALUES (";
                    foreach($data AS $key => $row) {
                        $query .= $key . ", ";
                    }
                    $query = substr($query, 0, -2) . ")";
                    // Ejecuta SQL.
                    return self::query($query, $data);
            	} catch(\PDOException $e) {
                    $errors[] = "Error al actualizar los datos del usuario: " . $e->getMessage();
                    return false;
    			}
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
            $required = array(
                'email' => true
            );

            // Nuevo usuario.
            if(empty($this->id)) {
                $required['password'] = true;

                // Nombre de usuario (id)
                if(empty($this->name)) {
                    $errors['username'] = Text::get('error-register-username');
                }
                else {
                    $id = self::idealiza($this->name);
                    $query = self::query('SELECT id FROM user WHERE id = ?', array($id));
                    if($query->fetchColumn()) {
                        $errors['username'] = Text::get('error-register-user-exists');
                    }
                }

                // E-mail
                if(!empty($this->email)) {
                    $query = self::query('SELECT email FROM user WHERE email = ?', array($this->email));
                    if($query->fetchObject()) {
                        $errors['email'] = Text::get('error-register-email-exists');
                    }
                }
                else {
                    $errors['email'] = Text::get('error-register-email');
                }

                // Contraseña
                if(!empty($this->password)) {
                    if(strlen($this->password)<8) {
                        $errors['password'] = Text::get('error-register-short-password');
                    }
                }
                else {
                    $errors['password'] = Text::get('error-register-pasword');
                }
            }
            // Modificar usuario.
            else {
                if (empty($this->name)) {
                    $errors['name'] = Text::get('validate-user-field-name');
                }
                if (empty($this->avatar)) {
                    $errors['avatar'] = Text::get('validate-user-field-avatar');
                }
                if (empty($this->about)) {
                    $errors['about'] = Text::get('validate-user-field-about');
                }
                if (empty($this->interests)) {
                    $errors['interests'] = Text::get('validate-user-field-interests');
                }
                $keywords = explode(',', $this->keywords);
                if (sizeof($keywords) < 5) {
                    $errors['keywords'] = Text::get('validate-user-field-keywords');
                }
                if (empty($this->contribution)) {
                    $errors['contribution'] = Text::get('validate-user-field-contribution');
                }
                if (empty($this->webs)) {
                    $errors['webs'] = Text::get('validate-user-field-webs');
                }
                if (empty($this->facebook)) {
                    $errors['facebook'] = Text::get('validate-user-field-facebook');
                }
                if (empty($this->twitter)) {
                    $errors['twitter'] = Text::get('validate-user-field-twitter');
                }
                if (empty($this->linkedin)) {
                    $errors['linkedin'] = Text::get('validate-user-field-linkedin');
                }
            }
            // @TODO: Revisar $required (Ajuste temporal)
            foreach($required AS $item => $value) {
                if(in_array($item, $errors)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Usuario.
         *
         * @param string $id    Nombre de usuario
         * @return obj|false    Objeto de usuario, en caso contrario devolverÃ¡ 'false'.
         */
        public static function get ($id) {
            try {
                $query = static::query("
                    SELECT
                        id,
                        role_id AS role,
                        email,
                        name,
                        avatar,
                        about,
                        contribution,
                        keywords,
                        facebook,
                        twitter,
                        linkedin,
                        active,
                        worth,
                        created,
                        modified
                    FROM user
                    WHERE id = :id
                    ", array(':id' => $id));
                $user = $query->fetchObject(__CLASS__);
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
		 * @param string $password ContraseÃ±a
		 * @return obj|false Objeto del usuario, en caso contrario devolverÃ¡ 'false'.
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
		 * Comprueba si el usuario estÃ¡ identificado.
		 *
		 * @return boolean
		 */
		public static function isLogged() {
			return !empty($_SESSION['user']);
		}

	}
}