<?php

namespace Goteo\Model {

	use Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Model\Image,
        Goteo\Library\Mail,
        Goteo\Library\Check,
        Goteo\Library\Message;

	class User extends \Goteo\Core\Model {

        public
            $id = false,
            $email,
            $name,
            $location,
            $avatar = false,
            $about,
            $contribution,
            $keywords,
            $active,
            $facebook,
            $twitter,
            $identica,
            $linkedin,
            $created,
            $modified,
            $interests = array(),
            $webs = array(),
            $roles = array();

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
                    $data[':id'] = $this->id = static::idealiza($this->name);
                    $data[':name'] = $this->name;
                    $data[':location'] = $this->location;
                    $data[':email'] = $this->email;
                    $data[':token'] = $token = md5(uniqid());
                    $data[':password'] = sha1($this->password);
                    $data[':created'] = date('Y-m-d H:i:s');
                    $data[':active'] = false;

                    // Rol por defecto.
                    static::query('INSERT INTO user_role (user_id, role_id, node_id) VALUES (:user, :role, :node);', array(
                        ':user' => $this->id,
                    	':role' => 'user',
                    	':node' => '*',
                    ));

                    // Activación
                    $mail = new Mail();
                    $mail->to = $this->email;
                    $mail->toName = $this->name;
                    $mail->subject = Text::get('subject-register');
                    $url = SITE_URL . '/user/activate/' . $token;
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
                            self::query("REPLACE user_image (user, image) VALUES (:user, :image)", array(':user' => $this->id, ':image' => $image->id));
                        }
                    }

                    // Perfil público
                    if(isset($this->name)) {
                        $data[':name'] = $this->name;
                    }

                    // Dónde está
                    if(isset($this->location)) {
                        $data[':location'] = $this->location;
                    }

                    if(isset($this->about)) {
                        $data[':about'] = $this->about;
                    }

                    if(isset($this->keywords)) {
                        $data[':keywords'] = $this->keywords;
                    }

                    if(isset($this->contribution)) {
                        $data[':contribution'] = $this->contribution;
                    }

                    if(isset($this->facebook)) {
                        $data[':facebook'] = $this->facebook;
                    }

                    if(isset($this->twitter)) {
                        $data[':twitter'] = $this->twitter;
                    }

                    if(isset($this->identica)) {
                        $data[':identica'] = $this->identica;
                    }

                    if(isset($this->linkedin)) {
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
                    static::query('DELETE FROM user_web WHERE user= ?', $this->id);
                    if (!empty($this->webs)) {
                        foreach ($this->webs as $web) {
                            if ($web instanceof User\Web) {
                                $web->user = $this->id;
                                $web->save($errors);
                            }
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
        public function validate (&$errors = array(), &$okeys = array()) {
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
                if (empty($this->email)) {
                    $errors['email'] = Text::get('mandatory-register-field-email');
                } elseif (!Check::mail($this->email)) {
                    $errors['email'] = Text::get('validate-register-value-email');
                } else {
                    $query = self::query('SELECT email FROM user WHERE email = ?', array($this->email));
                    if($query->fetchObject()) {
                        $errors['email'] = Text::get('error-register-email-exists');
                    }
                }

                // Contraseña
                if(!empty($this->password)) {
                    if(!Check::password($this->password)) {
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
                    elseif(!Check::mail($this->email)) {
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
                    if(!Check::password($this->password)) {
                        $errors['password'] = Text::get('error-user-password-invalid');
                    }
                }

                if (is_array($this->avatar) && !empty($this->avatar['name'])) {
                    $image = new Image($this->avatar);
                    $_err = array();
                    $image->validate($_err);
                    $errors['avatar'] = $_err['image'];
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
                        email,
                        name,
                        location,
                        avatar,
                        about,
                        contribution,
                        keywords,
                        facebook,
                        twitter,
                        identica,
                        linkedin,
                        active,
                        created,
                        modified
                    FROM user
                    WHERE id = :id
                    ", array(':id' => $id));
                $user = $query->fetchObject(__CLASS__);
                
                $user->roles = $user->getRoles();
                $user->avatar = Image::get($user->avatar);
                $user->interests = User\Interest::get($id);
                $user->webs = User\Web::get($id);
                return $user;
            } catch(\PDOException $e) {
                return false;
            }
        }

        // version mini de get para sacar nombre i mail
        public static function getMini ($id) {
            try {
                $query = static::query("
                    SELECT
                        name,
                        email
                    FROM user
                    WHERE id = :id
                    ", array(':id' => $id));
                $user = $query->fetchObject(__CLASS__);
                
                $user->avatar = Image::get($user->avatar);

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
        public static function getAll ($filters = array()) {
            $users = array();

            $sqlFilter = "";
            if (!empty($filters['status'])) {
                $active = $filters['status'] == 'active' ? '1' : '0';
                $sqlFilter .= " AND active = '$active'";
            }
            if (!empty($filters['interest'])) {
                $sqlFilter .= " AND id IN (
                    SELECT user
                    FROM user_interest
                    WHERE interest = {$filters['interest']}
                    ) ";
            }
            if (!empty($filters['posted'])) {
                /*
                 * Si ha enviado algun mensaje o comentario
                $sqlFilter .= " AND id IN (
                    SELECT user
                    FROM message
                    WHERE interest = {$filters['interest']}
                    ) ";
                 *
                 */
            }

            $sql = "SELECT
                        id,
                        name,
                        email,
                        active
                    FROM user
                    WHERE id != ''
                        $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, array($node));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $user) {

                $query = static::query("
                    SELECT
                        user_id
                    FROM user_role
                    WHERE user_id = :id
                    AND role_id = 'checker'
                    ", array(':id' => $user->id));
                $role = $query->fetchObject();

                if ($role->user_id == $user->id) {
                    $user->checker = true;
                }

                $users[] = $user;
            }
            return $users;
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
			    $user = static::get($row['id']);
			    if($user->active) {
			        return $user;

			    }
			    else {
			        Message::Error(Text::get('user-account-inactive'));
			    }
			}
			return false;
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
		 * Verificacion de recuperacion de contraseña
		 *
		 * @param string $username Nombre de usuario
		 * @param string $email    Email de la cuenta
		 * @return boolean true|false  Correctos y mail enviado
		 */
		public static function recover ($username, $email) {
            $query = self::query("
                    SELECT
                        id,
                        name,
                        email
                    FROM user
                    WHERE BINARY id = :username
                    AND BINARY email = :email",
				array(
					':username' => trim($username),
					':email'    => trim($email)
				)
			);
			if($row = $query->fetchObject()) {
                // tenemos id, nombre, email
                // genero el token
                $token = md5(uniqid()) . '¬' . $row->email;
                self::query('UPDATE user SET token = :token WHERE id = :id', array(':id' => $row->id, ':token' => $token));

                // Email de recuperacion
                $mail = new Mail();
                $mail->to = $row->email;
                $mail->toName = $row->name;
                $mail->subject = 'Su petición de recuperación de contraseña en Goteo';
                $url = SITE_URL . '/user/recover/' . base64_encode($token);
                $mail->content = sprintf('
                    Estimado(a) <strong>%1$s</strong>:<br/>
                    <br/>
                    Hemos recibido una petición para recuperar la contraseña de tu cuenta de usuario en Goteo.org<br />
                    Si no has solicitado esta recuperación de contraseña, ignora este mensaje<br />
                    Para acceder a tu cuenta y cambiar la contraseña (utilice su nombre de usuario como contraseña actual), utiliza el siguiente enlace. Si no puedes hacer click, copialo y pegalo en el navegador.
                    <br/>
                    <a href="%2$s">%2$s</a><br/>
                    <br/>
                    Recuerde que su nombre de usuario es <strong>%3$s</strong>, póngalo como contraseña actual para cambiar la contraseña.<br/>
                    Hasta pronto!
                ', $row->name, $url, $row->id);
                $mail->html = true;
                if ($mail->send($errors)) {
                    return true;
                }
			}
			return false;
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
                if(Check::mail($email)) {
                    $mail = new Mail();
                    $mail->to = $email;
                    $mail->toName = $this->name;
                    $mail->subject = Text::get('subject-change-email');
                    $url = SITE_URL . '/user/changeemail/' . base64_encode($token);
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
            $query = self::query('SELECT DISTINCT(project) FROM invest WHERE user = ? AND status <> 2 AND (anonymous = 0 OR anonymous IS NULL)', array($this->id));
            $projects = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query = self::query('SELECT SUM(amount), COUNT(id) FROM invest WHERE user = ? AND status <> 2', array($this->id));
            $invest = $query->fetch();
            return array('projects' => $projects, 'amount' => $invest[0], 'invests' => $invest[1]);
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

        /**
         * Valores por defecto actuales para datos personales
         *
         * @return type array
         */
        public static function getPersonal ($id) {
            $query = self::query('SELECT
                                      contract_name,
                                      contract_nif,
                                      phone,
                                      address,
                                      zipcode,
                                      location,
                                      country
                                  FROM user_personal
                                  WHERE user = ?'
                , array($id));

            $data = $query->fetchObject();
            return $data;
        }

        /**
         * Actualizar los valores personales
         *
         * @params force boolean  (REPLACE data when true, only if empty when false)
         * @return type booblean
         */
        public static function setPersonal ($user, $data = array(), $force = false, &$errors = array()) {

            if ($force) {
                // actualizamos los datos
                $ins = 'REPLACE';
            } else {
                // solo si no existe el registro
                $ins = 'INSERT';
                $query = self::query('SELECT user FROM user_personal WHERE user = ?', array($user));
                if ($query->fetchColumn(0) == $user) {
                    return false;
                }
            }


            $fields = array(
                  'contract_name',
                  'contract_nif',
                  'phone',
                  'address',
                  'zipcode',
                  'location',
                  'country'
            );

            $values = array();
            $set = '';

            foreach ($data as $key=>$value) {
                if (in_array($key, $fields)) {
                    $values[":$key"] = $value;
                    if ($set != '') $set .= ', ';
                    $set .= "$key = :$key";
                }
            }

            if (!empty($values) && $set != '') {
                    $values[':user'] = $user;
                    $sql = "$ins INTO user_personal SET user = :user, " . $set;

                try {
                    self::query($sql, $values);
                    return true;

                } catch (\PDOException $e) {
                    $errors[] = "FALLO al gestionar el registro de fdatos personales " . $e->getMessage();
                    return false;
                }
            }


        }

		private function getRoles () {
		    $query = self::query('
		    	SELECT
		    		role.id,
		    		role.name
		    	FROM role
		    	JOIN user_role ON role.id = user_role.role_id
		    	WHERE user_id = ?
		    ', array($this->id));
		    return $query->fetchAll(\PDO::FETCH_OBJ);
		}

	}
}
