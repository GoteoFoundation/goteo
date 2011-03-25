<?php

namespace Goteo\Controller {

	use Goteo\Library\Text,
		Goteo\Library\Lang;

	class Texts extends \Goteo\Core\Controller {
		
		public function index ($lang = 'es') {

			// si tenemos usuario logueado
			$id = $_SESSION['user'];

			if (!$id || $id != 'root') {
				header('Location: /');
				die;
			}

			$content = '';

			$using = Lang::get($lang);
			$content .= '<a href="/dashboard">Volver al panel</a><br />';
			$content .= "Viendo textos en {$using->name}<br /><hr />";
			
			$texts = Text::getAll($lang);

			foreach ($texts as $text) {
				$urlText = rawurlencode($text->id);
				$purpose = Text::getPurpose($text->id);
				$content .= <<<EOD
					<strong>{$text->id} :</strong><br />
					<span style="font-style:italic;">{$purpose}</span><br />
					&gt; {$text->text}<br />
					<a href='/texts/translate/{$urlText}'>Gestionar</a><br />
					<hr />

EOD;
			}

			echo $content;
//            include 'view/index.html.php';
		}
		
		public function translate ($text = null, $lang = 'es') {

			// si tenemos usuario logueado
			$id = $_SESSION['user'];

			if (!$id || $id != 'root') {
				header('Location: /');
				die;
			}

			$content = '';

			$using = Lang::get($lang);
			$content .= "Editando {$using->name}<hr />";
			$content .= '<a href="/texts">Volver</a>';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				$errors = array();

				$data = array(
					'id' => $text,
					'text' => $_POST['newtext'],
					'lang' => $lang
				);

				if (Text::save($data, $errors)) {
					header('Location: /texts');
					die;
				}
				else {
					foreach ($errors as $error) {
						$content .= '<span style="color:red;">' . $error . '</span><br />';
					}
				}
			}
			
			$es_text = Text::get($text, 'es');
			$lang_text = Text::get($text, $lang);
			$purpose = Text::getPurpose($text);

			$content .= <<<EOD
				<form action="/texts/translate/{$text}/{$lang}" method="post">
					<span style="font-style:italic;">{$purpose}</span><br />
					<p>{$es_text}</p>
					<dl>
						<dt><label for="newtext">{$text}</label></dt>
						<dd>{$lang}<textarea id="newtext" name="newtext" cols="100" rows="6">{$lang_text}</textarea></dd>
					</dl>
					<input type="submit" name="translate" value="Aplicar" />
				</form>
EOD;

			echo $content;
//            include 'view/index.html.php';
		}

	}
	
}