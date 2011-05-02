<?php

namespace Goteo\Model {

	use Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Library\Image,
        Goteo\Library\Mail;

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
            $worth,
            $created,
            $modified,
            $interests = array(),
            $webs = array();

	    public function __set ($name, $value) {
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

                    if(!empty($this->email)) {
                        if(!empty($this->token)) {
                            if($this->token == $this->getToken()) {
                                $data[':email'] = $this->email;
                                $data[':token'] = null;
                            }
                        }
                        else {
                            $mail = new Mail();
                            $mail->to = $this->email;
                            $mail->toName = $this->name;
                            $mail->subject = Text::get('subject-change-email');
                            $token = md5(uniqid()) . $this->email;
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

                            $data[':token'] = $token;
                        }
                    }

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
                         * @FIXME Relación NM user_image
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
                    // Primero elimino TODAS las webs y luego las volveré a
                    // añadir.
                    static::query('DELETE FROM user_web WHERE user= ?', $this->id);
                    
                    if (!empty($this->webs)) {                        
                        foreach ($this->webs as $web) {                            
                            if ($web instanceof User\Web) {
                                $web->user = $this->id;
                                $web->save($errors);
                            }
                        }                                                
                    }
                    
                    /*
                    
                     $query = static::query("SELECT id, user, url FROM user_web WHERE user = ?", array($id));
                    $webs = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
                    
                    
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
                            if(array_key_exists($web->id, $_POST['user_webs']['edit'])) {
                                $web->user = $this->id;
                                $web->url = $_POST['user_webs']['edit'][$web->id];
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
                     * 
                     */
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
                    //$_POST = array();
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
                return empty($errors);
            }
            // Modificar usuario.
            else {
                // E-mail
                if(!empty($this->password)) {
                    if(false) { // @FIXME: Validar formato dirección de correo.
                        $errors['email'] = Text::get('error-register-email-invalid');
                    }
                }

                // Contraseña
                if(!empty($this->password)) {
                    if(strlen($this->password)<8) {
                        $errors['password'] = Text::get('error-register-short-password');
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
                        worth,
                        created,
                        modified,
                        token
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
		 */
		public static function flush () {
    		if(static::isLogged()) {
    			return $_SESSION['user'] = self::get($_SESSION['user']->id);
    		}
    	}

    	/**
    	 * Token de verificación.
    	 * @return type string
    	 */
    	private function getToken () {
            $query = self::query('SELECT token FROM user WHERE id = ?', array($this->id));
            return $query->fetchColumn(0);
    	}

	}
}