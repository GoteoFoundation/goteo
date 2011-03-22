<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Model\User as Usr;

	class User extends \Goteo\Core\Controller {

		/*
		 *  Página pública de usuario
		 *  si no tenemos el id del usuario en la url debería saltar a otra página
		 */
		public function index ($id = null) {

			if ($id === null) {
				header('Location: /');
				die;
			}

			/*
			$content = new Model\Content('user');
			 * 
			 */
			$message = "Perfil público del usuario $id <br />";

			// saca los datos del usuario, si no existe tendria que enviarlo a la portada
			$user = new Usr($id);
			$message .= '<pre>' . print_r($user, 1) . '</pre>';


			include 'view/index.html.php';
		}

		/*
		 *  Para loguear se puede usar el email o el user name (campo `user`)
		 */
        public function login () {

			$content = '';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$id = Usr::validate($_POST['user'], $_POST['pass']);
				if ($id != false) {
					header('Location: /dashboard/' . $id);
					die;
				}
				else {
					$content .= '<span style="color:red;">Error en la validación</span><hr />';
				}
			}
			/*
			 @TODO
			$content = new Model\Content('user-login');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content .= <<<EOD
				<div id="validate">
					<form action="/user/login" method="post">
						<dl>
							<dt><laber for="user">Usuario</label></dt>
							<dd><input type="text" id="user" name="user" value=""/></dd>
							<dt><laber for="pass">Contrase&ntilde;a</label></dt>
							<dd><input type="password" id="pass" name="pass" value=""/></dd>
						</dl>
						<input type="submit" name="login" value="Accede" />
					</form>
				</div>
				|
				<div id="register">
					<a href="/user/register">Reg&iacute;strate</a>
				</div>
EOD;
			
            include 'view/user/login.html.php';

        }

        public function register () {

			$content = '';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				$errors = array();
				// comprobamos lo que quieren registrar
				$checked = Usr::check($_POST, $errors);

				if ($checked === false) {
					foreach ($errors as $error) {
						$content .= '<span style="color:red;">' . $error . '</span><br />';
					}
					$content .= '<pre>' . print_r($_POST, 1) . '</pre>';
				} 
				else {
					// ok, lo creamos
					$data = array(
						'user'=>$_POST['user'],
						'email'=>$_POST['email'],
						'pass'=>$_POST['pass']
					);

					$user = new User();
					$user->create($data);
					if ($user->id) {
						// lo pasamos por la validación
						header('Location: /user/profile/' . $user->id);
					}
					else {
						throw new Error('No se ha creado el usuario');
					}
				}
			}
			else {
			/*
			 @TODO
			$content = new Model\Content('user-register');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */

				$content .= <<<EOD
				<div>
					<form action="/user/register" method="post">
						<dl>
							<dt><laber for="user">Nombre de usuario *</label></dt>
							<dd><input type="text" id="user" name="user" value=""/></dd>
							<dt><laber for="email">Email *</label></dt>
							<dd><input type="text" id="email" name="email" value=""/></dd>
							<dt><laber for="cemail">Confirmar email *</label></dt>
							<dd><input type="text" id="cemail" name="cemail" value=""/></dd>
							<dt><laber for="pass">Contrase&ntilde;a *</label></dt>
							<dd><input type="password" id="pass" name="pass" value=""/></dd>
							<dt><laber for="cpass">Confirmar contrase&ntilde;a *</label></dt>
							<dd><input type="password" id="cpass" name="cpass" value=""/></dd>
						</dl>
						<input type="submit" name="register" value="Enviar" />
					</form>
				</div>
EOD;
			}
			
            include 'view/user/register.html.php';
            
        }

		/*
		 *  este $id no vendrá por aqui una vez tengamos la validación de usuario
		 */
        public function edit ($id = null) {

			$user = new Usr($id);
//			echo '<pre>' . print_r($user, 1) . '</pre>';
			$content = '';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//				$content .= '<pre>' . print_r($_POST, 1) . '</pre>';
				$errors = array();
				if ($user->save($_POST, $errors)) {
					$content .= 'Datos guardados<hr />';
				}
				else {
					foreach ($errors as $k=>$error) {
						$content .= '<span syle="color:red">' . $error . '</span><br />';
					}
					$content .= '<br />Error al guardar los datos<hr />';
				}
			}
			
			/*
			 @TODO
			$content = new Model\Content('user-edit');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content .= <<<EOD
				<div>
					<form action="/user/edit/{$user->id}" method="post">
						Nombre de usuario actual: {$user->user}<br />
						Email actual: {$user->email}<br />
						<dl>
							<dt><laber for="nuser">Nuevo nombre de usuario</label></dt>
							<dd><input type="text" id="nuser" name="nuser" value=""/></dd>
						<hr />
							<dt><laber for="nemail">Nuevo email</label></dt>
							<dd><input type="text" id="nemail" name="nemail" value=""/></dd>
							<dt><laber for="nemail">Confirmar nuevo email</label></dt>
							<dd><input type="text" id="cemail" name="cemail" value=""/></dd>
						<hr />
							<dt><laber for="pass">Contrase&ntilde;a antigua</label></dt>
							<dd><input type="password" id="pass" name="pass" value=""/></dd>
							<dt><laber for="npass">Contrase&ntilde;a nueva</label></dt>
							<dd><input type="password" id="npass" name="npass" value=""/></dd>
							<dt><laber for="cpass">Confirmar contrase&ntilde;a</label></dt>
							<dd><input type="password" id="cpass" name="cpass" value=""/></dd>
						</dl>
						<input type="submit" name="edit" value="Guardar cambios" />
					</form>
				</div>
EOD;
			
			
            include 'view/user/edit.html.php';
            
        }
        
		/*
		 *  este $id no vendrá por aqui una vez tengamos la validación de usuario
		 */
        public function profile ($id = null) {

			$user = new Usr($id);
			$content = '';
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//				$content .= '<pre>' . print_r($_POST, 1) . '</pre>';
				// @TODO hay que hacer el tratamiento de la imagen
				$_POST['avatar'] = $_POST['image'];
				$errors = array();
				if ($user->update($_POST, $errors)) {
					$content .= 'Datos guardados<hr />';
				}
				else {
					foreach ($errors as $k=>$error) {
						$content .= '<span syle="color:red">' . $error . '</span><br />';
					}
					$content .= '<br />Error al guardar los datos<hr />';
				}
			}
			
			/*
			 @TODO
			$content = new Model\Content('user-profile');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content .= <<<EOD
				<div>
					<form action="/user/profile/{$id}" method="post">
						<dl>
							<dt><laber for="name">Nombre completo</label></dt>
							<dd><input type="text" id="name" name="name" value="{$user->name}"/></dd>

							<dt><laber for="image">Tu imagen</label></dt>
							<dd><input type="file" id="theimage" name="theimage" value=""/> img src="{$user->avatar}" </dd>
							<input type="text" name="image" value="avatar.jpg" /> <- como texto hasta tener el tratamiento de imagen

							<dt><laber for="about">Cu&eacute;ntanos algo sobre t&iacute;</label></dt>
							<dd><textarea id="about" name="about">{$user->about}</textarea></dd>

							<dt><laber for="interests">Intereses</label></dt>
							<dd><input type="text" id="interests" name="interests" value="{$user->interests}"/></dd>

							<dt><laber for="contribution">Qué podrías aportar a Goteo</label></dt>
							<dd><textarea id="contribution" name="contribution">{$user->contribution}</textarea></dd>

							<dt><laber for="blog">Blog</label></dt>
							<dd>http://<input type="text" id="blog" name="blog" value="{$user->blog}"/></dd>

							<dt><laber for="twitter">Twitter</label></dt>
							<dd>http://twitter.com/<input type="text" id="twitter" name="twitter" value="{$user->twitter}"/></dd>

							<dt><laber for="facebook">Facebook</label></dt>
							<dd>http://facebook.com/<input type="text" id="facebook" name="facebook" value="{$user->facebook}"/></dd>

							<dt><laber for="linkedin">Linkedin</label></dt>
							<dd>http://linkedin.com/<input type="text" id="linkedin" name="linkedin" value="{$user->linkedin}"/></dd>

						</dl>
						<input type="submit" name="profile" value="Aplicar cambios" />
					</form>
				</div>
EOD;
			
            include 'view/user/profile.html.php';

        }

    }
    
}
