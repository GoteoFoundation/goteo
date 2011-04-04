<?php

namespace Goteo\Model {

	use Goteo\Core\Redirection;

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
                        blog,
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
         * //@TODO cambiar los textos a Text::get cuando el cliente lo verifique
         * @param array $errors por referencia
         */
        public function check(&$errors = array()) {
            $score =  0;
            $max = 0;

            if (empty($this->name)) {
                $errors['user-name'] = 'Pon tu nombre completo para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            if (empty($this->avatar)) {
                $errors['user-avatar'] = 'Pon una imagen de perfil para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            if (empty($this->about)) {
                $errors['user-about'] = 'Cuenta algo sobre ti para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            if (empty($this->interests)) {
                $errors['user-interests'] = 'Selecciona algún interés para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            $keywords = explode(',', $this->keywords);
            $score += count($keywords) > 5 ? 5 : count($keywords);
            if ($keywords < 5) {
                $errors['user-keywords'] = 'Indica hasta 5 palabras clave que te definan para mejorar la puntuación';
            }
            $max += 5;

            if (empty($this->contribution)) {
                $errors['user-contribution'] = 'Explica que podrias aportar en Goteo para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            if (empty($this->blog)) {
                $errors['user-blog'] = 'Pon tu página web para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            if (empty($this->facebook)) {
                $errors['user-facebook'] = 'Pon tu cuenta de facebook para mejorar la puntuación';
                --$score;
            } else {
                ++$score;
            }
            ++$max;

            return array('score'=>$score,'max'=>$max);
        }

        /**
         * Metodo para guardar la información del usuario desde el primer paso del formulario de proyecto
         * @param array $errors por referencia
         */
        public function saveInfo(&$errors = array()) {

            //@TODO validate (pero estos campos son de contenido libre excepto quizás las url)

            $fields = array(
                'name',
                'avatar',
                'about',
                'keywords',
                'contribution',
                'blog',
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
                echo "$sql <pre>" . print_r($values, 1) ."</pre><br />";
                echo $e->getMessage();
                return false;
			}


        }

	}
}