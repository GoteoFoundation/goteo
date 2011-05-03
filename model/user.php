<?php

namespace Goteo\Model {

	use Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Library\Image,
        Goteo\Library\Mail,
        Goteo\Library\Check;

	class User extends \Goteo\Core\Model {

        public
            $id = false,
            $role = null,
            $email,
            $name,
            $avatar = false,
            $about,
            $contribution,
            $keywords,
            $active,
            $facebook,
            $twitter,
            $linkedin,
            $country,
            $created,
            $modified,
            $interests = array(),
            $webs = array();

        /**
         * Sobrecarga de métodos 'setter'.
         *
         * @param type string	$name
         * @param type string	$value
         */
        public function __set ($name, $value) {
	        if($name == "token") {
	            $this->$name = $this->setToken($value);
	        }
            $this->$name = $value;
        }

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "token") {
	            return $this->getToken();
	        }
	        if($name == "support") {
	            return $this->getSupport();
	        }
	        if($name == "worth") {
	            return $this->getWorth();
	        }
            return $this->$name;
        }

        /**
         * Guardar usuario.
         * Guarda los valores de la instancia del usuario en la tabla.
         *
         * @param type array	$errors     Errores devueltos pasados por referencia.
         * @return type bool	true|false
         */
        public function save (&$errors = array()) {
            if($this->validate($errors)) {
                // Nuevo usuario.
                if(empty($this->id)) {
                    $insert = true;
                    $this->id = static::idealiza($this->name);
                    $data[':id'] = $this->id;
                    $data[':role_id'] = 3; // @FIXME: Provisionalmente: 3 = Usuario
                    $data[':name'] = $this->name;
                    $data[':email'] = $this->email;
                    $data[':password'] = sha1($this->password);
                    $data[':created'] = date('Y-m-d H:i:s');
                    $data[':active'] = false;

                    // Activación
                    $mail = new Mail();
                    $mail->to = $this->email;
                    $mail->toName = $this->name;
                    $mail->subject = Text::get('subject-register');
                    $token = date("YmdHis", \date2time($data[':created'])) . $this->id;
                    $url = 'http://goteo.org/user/activate/' . base64_encode($token);
                    $mail->content = sprintf('
                        Estimado(a) <strong>%1$s</strong>:<br/>
                        <br/>
                        Gracias por registrase en Goteo.org, su nueva cuenta ha sido creada con éxito.<br/>
                        Para activar su cuenta y confirmar su dirección de correo electrónico, haga clic en el siguiente vínculo (o copie y pégue el enlace en la barra de dirección de su navegador):<br/>
                        <br/>
                        <a href="%2$s">%2$s</a><br/>
                        <br/>
                        Recuerde que su nombre de usuario es <strong>%3$s</strong>, una vez que active su cuenta podrá acceder y disfrutar de los servicios que le ofrecemos.<br/>
                        <br/>
                        Si usted no ha solicitado el registro, simplemente ignore este mensaje.
					', $this->name, $url, $this->id);
                    $mail->html = true;
                    $mail->send($errors);
                }
                else {
                    $data[':id'] = $this->id;

                    // E-mail
                    if(!empty($this->email)) {
                        if(count($tmp = explode('¬', $this->email)) > 1) {
                            $data[':email'] = $tmp[1];
                            $data[':token'] = null;
                        }
                        else {
                            $query = self::query('SELECT email FROM user WHERE id = ?', array($this->id));
                            if($this->email !== $query->fetchColumn()) {
                                $this->token = md5(uniqid()) . '¬' . $this->email;
                            }
                        }
                    }

                    // Contraseña
                    if(!empty($this->password)) {
                        $data[':password'] = sha1($this->password);
                    }

                    if(!is_null($this->active)) {
                        $data[':active'] = $this->active;
                    }

                    // Avatar
                    if (is_array($this->avatar) && !empty($this->avatar['name'])) {
                        $image = new Image($this->avatar);
                        $image->save();
                        $data[':avatar'] = $image->id;

                        /**
                         * Guarda la relación NM en la tabla 'user_image'.
                         */
                        if(!empty($image->id)) {
                            self::query("REPLACE user_image (user_id, image_id) VALUES (:user, :image)", array(':user' => $this->id, ':image' => $image->id));
                        }
                    }

                    // Perfil público
                    if(!empty($this->name)) {
                        $data[':name'] = $this->name;
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

                    // Intereses
                    $interests = User\Interest::get($this->id);
                    if(!empty($this->interests)) {
                        foreach($this->interests as $interest) {
                            if(!in_array($interest, $interests)) {
                                $_interest = new User\Interest();
                                $_interest->id = $interest;
                                $_interest->user = $this->id;
                                $_interest->save($errors);
                                $interests[] = $_interest;
                            }
                        }
                    }
                    foreach($interests as $key => $interest) {
                        if(!in_array($interest, $this->interests)) {
                            $_interest = new User\Interest();
                            $_interest->id = $interest;
                            $_interest->user = $this->id;
                            $_interest->remove($errors);
                        }
                    }

                    // Webs
                    if(!empty($this->webs)) {
                        // Eliminar
                        $webs = User\Web::get($this->id);
                        foreach($webs as $web) {
                            if(array_key_exists($web->id, $this->webs['remove'])) {
                                $web->remove($errors);
                            }
                        }
                        // Modificar
                        $webs = User\Web::get($this->id);
                        foreach($webs as $web) {
                            if(array_key_exists($web->id, $this->webs['edit'])) {
                                $web->user = $this->id;
                                $web->url = $this->webs['edit'][$web->id];
                                $web->save($errors);
                            }
                        }
                        // Añadir
                        foreach($this->webs['add'] as $web) {
                            $_web = new User\Web();
                            $_web->user = $this->id;
                            $_web->url = $web;
                            $_web->save($errors);
                        }
                    }
                }

                try {
                    // Construye SQL.
                    if(isset($insert) && $insert == true) {
                        $query = "INSERT INTO user (";
                        foreach($data AS $key => $row) {
                            $query .= substr($key, 1) . ", ";
                        }
                        $query = substr($query, 0, -2) . ") VALUES (";
                        foreach($data AS $key => $row) {
                            $query .= $key . ", ";
                        }
                        $query = substr($query, 0, -2) . ")";
                    }
                    else {
                        $query = "UPDATE user SET ";
                        foreach($data AS $key => $row) {
                            if($key != ":id") {
                                $query .= substr($key, 1) . " = " . $key . ", ";
                            }
                        }
                        $query = substr($query, 0, -2) . " WHERE id = :id";
                    }
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
        public function validate (&$errors = array()) {
            // Nuevo usuario.
            if(empty($this->id)) {
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
                    $errors['email'] = Text::get('error-register-email-empty');
                }

                // Contraseña
                if(!empty($this->password)) {
                    if(!Check::Password($this->password)) {
                        $errors['password'] = Text::get('error-register-invalid-password');
                    }
                }
                else {
                    $errors['password'] = Text::get('error-register-pasword-empty');
                }
                return empty($errors);
            }
            // Modificar usuario.
            else {
                if(!empty($this->email)) {
                    if(count($tmp = explode('¬', $this->email)) > 1) {
                        if($this->email !== $this->token) {
                            $errors['email'] = Text::get('error-user-email-token-invalid');
                        }
                    }
                    elseif(!Check::Mail($this->email)) {
                        $errors['email'] = Text::get('error-user-email-invalid');
                    }
                    else {
                        $query = self::query('SELECT id FROM user WHERE email = ?', array($this->email));
                        if($found = $query->fetchColumn()) {
                            if($this->id !== $found) {
                                $errors['email'] = Text::get('error-user-email-exists');
                            }
                        }
                    }
                }
                if(!empty($this->password)) {
                    if(!Check::Password($this->password)) {
                        $errors['password'] = Text::get('error-user-password-invalid');
                    }
                }
                if (empty($this->name)) {
                    $errors['name'] = Text::get('validate-user-field-name');
                }
                if (is_array($this->avatar) && !empty($this->avatar['name'])) {
                    $image = new Image($this->avatar);
                    $_err = array();
                    $image->validate($_err);
                    $errors['avatar'] = $_err['image'];
                }
                elseif(!is_object($this->avatar)) {
                    $errors['avatar'] = Text::get('validate-user-field-avatar');
                }
                if (empty($this->about)) {
                    $errors['about'] = Text::get('validate-user-field-about');
                }
                $keywords = explode(',', $this->keywords);
                if (sizeof($keywords) < 5) {
                    $errors['keywords'] = Text::get('validate-user-field-keywords');
                }
                if (empty($this->contribution)) {
                    $errors['contribution'] = Text::get('validate-user-field-contribution');
                }
                if (empty($this->interests)) {
                    $errors['interests'] = Text::get('validate-user-field-interests');
                }
                if (empty($this->webs)) {
                    $errors['webs'] = Text::get('validate-user-field-webs');
                }
                else {
                    if(isset($this->webs['add'])) {
                        foreach($this->webs['add'] as $index => $web) {
                            if(empty($web)) {
                                unset($this->webs['add'][$index]);
                            }
                        }
                    }
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

            return (empty($errors['email']) && empty($errors['password']));
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
                        created,
                        modified
                    FROM user
                    WHERE id = :id
                    ", array(':id' => $id));
                $user = $query->fetchObject(__CLASS__);
                $user->avatar = Image::get($user->avatar);
                $user->interests = User\Interest::get($id);
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
        public static function getAll ($visible = true) {
            $query = self::query('SELECT * FROM user WHERE active = ?', array($visible));
            return $query->fetchAll(__CLASS__);
        }

		/**
		 * Validación de usuario.
		 *
		 * @param string $username Nombre de usuario
		 * @param string $password ContraseÃ±a
		 * @return obj|false Objeto del usuario, en caso contrario devolverÃ¡ 'false'.
		 */
		public static function login ($username, $password) {
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
		public static function isLogged () {
			return !empty($_SESSION['user']);
		}

		/**
		 * Refresca la sesión.
		 * (Utilizar después de un save)
		 *
		 * @return type object	User
		 */
		public static function flush () {
    		if(static::isLogged()) {
    			return $_SESSION['user'] = self::get($_SESSION['user']->id);
    		}
    	}

    	/**
    	 * Guarda el Token y envía un correo de confirmación.
    	 *
    	 * Usa el separador: ¬
    	 *
    	 * @param type string	$token	Formato: '<md5>¬<email>'
    	 * @return type bool
    	 */
    	private function setToken ($token) {
            if(count($tmp = explode('¬', $token)) > 1) {
                $email = $tmp[1];
                if(Check::Mail($email)) {
                    $mail = new Mail();
                    $mail->to = $email;
                    $mail->toName = $this->name;
                    $mail->subject = Text::get('subject-change-email');
                    $url = 'http://goteo.org/user/changeemail/' . base64_encode($token);
                    $mail->content = sprintf('
                        Estimado(a) <strong>%1$s</strong>:<br/>
                        <br/>
                        Para confirmar la propiedad de su nueva dirección de correo electrónico, haga clic en el siguiente vínculo (o copie y pégue el enlace en la barra de dirección de su navegador):<br/>
                        <br/>
                        <a href="%2$s">%2$s</a><br/>
                        <br/>
                        Esta proceso es necesario para confirmar la propiedad de su dirección de correo electrónico - no podrá operar con esta dirección hasta que la haya confirmado.
                    ', $this->name, $url);
                    $mail->html = true;
                    $mail->send();
                    return self::query('UPDATE user SET token = :token WHERE id = :id', array(':id' => $this->id, ':token' => $token));
                }
            }
    	}

    	/**
    	 * Token de confirmación.
    	 *
    	 * @return type string
    	 */
    	private function getToken () {
            $query = self::query('SELECT token FROM user WHERE id = ?', array($this->id));
            return $query->fetchColumn(0);
    	}

        /**
         * Cofinanciación.
         *
         * @return type array
         */
    	private function getSupport () {
            $query = self::query('SELECT DISTINCT(project) FROM invest WHERE user = ? AND status <> 2 AND anonymous <> 1', array($this->id));
            $projects = $query->fetchAll();
            $query = self::query('SELECT SUM(amount), COUNT(id) FROM invest WHERE user = ? AND status <> 2', array($this->id));
            $invest = $query->fetch();
            return array('projects' => $projects, 'amount' => $invest[0], 'count' => $invest[1]);
        }

	    /**
    	 * Nivel actual de meritocracia. (1-5)
    	 * [Recalcula y actualiza el registro en db]
    	 *
    	 * @return type int	Worth::id
    	 */
    	private function getWorth () {
            $query = self::query('SELECT id FROM worthcracy WHERE amount <= ? ORDER BY amount DESC LIMIT 1', array($this->support['amount']));
            $worth = $query->fetchColumn();
    	    $query = self::query('SELECT worth FROM user WHERE id = ?', array($this->id));
            if($worth !== $query->fetchColumn()) {
                self::query('UPDATE user SET worth = :worth WHERE id = :id', array(':id' => $this->id, ':worth' => $worth));
            }
            return $worth;
        }

	}
}