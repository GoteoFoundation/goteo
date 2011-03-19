<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Model;        

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
			$message = "Perfil público del usuario $id";

			// saca los datos del usuario, si no existe tendria que enviarlo a la portada
			$data = Model\User::get($id);
			$message .= '<pre>' . print_r($data, 1) . '</pre>';


			include 'view/index.html.php';
		}
                        
        public function login () {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$content = '<pre>' . print_r($_POST, 1) . '</pre>';
				$content .= 'validarlo y saltar a su dashboard';
			}
			else {
			/*
			$content = new Model\Content('user-login');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content = <<<EOD
				<div id="validate">
					<form action="/user/login" method="post">
						<dl>
							<dt><laber for="user">Usuario</label></dt>
							<dd><input type="text" id="user" name="user"/></dd>
							<dt><laber for="pass">Contrase&ntilde;</label></dt>
							<dd><input type="password" id="pass" name="pass"/></dd>
						</dl>
						<input type="submit" name="login" value="Accede" />
					</form>
				</div>
				|
				<div id="register">
					<a href="/user/register">Reg&iacute;strate</a>
				</div>
EOD;
			}
			
            include 'view/user/login.html.php';

        }

        public function register () {
            
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$content = '<pre>' . print_r($_POST, 1) . '</pre>';
				$content .= 'darlo de alta y saltar a profile';
			}
			else {
			/*
			$content = new Model\Content('user-register');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */

				$content = <<<EOD
				<div id="validate">
					<form action="/user/register" method="post">
						<dl>
							<dt><laber for="user">Usuario</label></dt>
							<dd><input type="text" id="user" name="user"/></dd>
							<dt><laber for="email">Email</label></dt>
							<dd><input type="text" id="email" name="email"/></dd>
							<dt><laber for="cemail">Confirmar email</label></dt>
							<dd><input type="text" id="cemail" name="cemail"/></dd>
							<dt><laber for="pass">Contrase&ntilde;</label></dt>
							<dd><input type="password" id="pass" name="pass"/></dd>
							<dt><laber for="cpass">Confirmar contrase&ntilde;</label></dt>
							<dd><input type="password" id="cpass" name="cpass"/></dd>
						</dl>
						<input type="submit" name="register" value="Enviar" />
					</form>
				</div>
EOD;
			}
			
            include 'view/user/register.html.php';
            
        }
        
        public function edit () {
            
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$content = '<pre>' . print_r($_POST, 1) . '</pre>';
				$content .= 'comprobar el cambio de usuario, email y contraseña, enviar los emails de verificación';
			}
			else {
			/*
			$content = new Model\Content('user-edit');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content = <<<EOD
				<div id="validate">
					<form action="/user/edit" method="post">
						<input type="hidden" name="user" value=""/>
						<input type="hidden" name="email" value=""/>
						<dl>
							<dt><laber for="nuser">Usuario</label></dt>
							<dd><input type="text" id="nuser" name="nuser" value=""/></dd>
							<dt><laber for="nemail">Email</label></dt>
							<dd><input type="text" id="nemail" name="nemail" value=""/></dd>
							<dt><laber for="pass">Contrase&ntilde; antigua</label></dt>
							<dd><input type="password" id="pass" name="pass"/></dd>
							<dt><laber for="npass">Contrase&ntilde; nueva</label></dt>
							<dd><input type="password" id="npass" name="npass"/></dd>
							<dt><laber for="cpass">Confirmar contrase&ntilde;</label></dt>
							<dd><input type="password" id="cpass" name="cpass"/></dd>
						</dl>
						<input type="submit" name="edit" value="Guardar cambios" />
					</form>
				</div>
EOD;
			}
			
            include 'view/user/edit.html.php';
            
        }
        
        public function profile () {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$content = '<pre>' . print_r($_POST, 1) . '</pre>';
				$content .= 'guardar los cambios';
			}
			else {
			/*
			$content = new Model\Content('user-profile');
			 * esto nos dará el objeto con el que la vista pintará lo de abajo
			 */
				$content = <<<EOD
				<div id="validate">
					<form action="/user/profile" method="post">
						<dl>
							<dt><laber for="name">Nombre completo</label></dt>
							<dd><input type="text" id="name" name="name" value=""/></dd>

							<dt><laber for="avatar">Tu imagen</label></dt>
							<dd><input type="file" id="avatar" name="avatar"/> <img src="imagen-actual.jpg" /></dd>

							<dt><laber for="about">Cu&eacute;ntanos algo sobre t&iacute;</label></dt>
							<dd><textarea id="about" name="about"></textarea></dd>

							<dt><laber for="interests">Intereses</label></dt>
							<dd><input type="text" id="interests" name="interests" value=""/></dd>

							<dt><laber for="contribution">Qué podrías aportar a Goteo</label></dt>
							<dd><textarea id="contribution" name="contribution"></textarea></dd>



							<dt><laber for="blog">Blog</label></dt>
							<dd><input type="text" id="blog" name="blog" value=""/></dd>

							<dt><laber for="twitter">Twitter</label></dt>
							<dd><input type="text" id="twitter" name="twitter" value=""/></dd>

							<dt><laber for="facebook">Facebook</label></dt>
							<dd><input type="text" id="facebook" name="facebook" value=""/></dd>

							<dt><laber for="linkedin">Linkedin</label></dt>
							<dd><input type="text" id="linkedin" name="linkedin" value=""/></dd>

						</dl>
						<input type="submit" name="profile" value="Aplicar cambios" />
					</form>
				</div>
EOD;
			}
			
            include 'view/user/profile.html.php';

        }

    }
    
}
