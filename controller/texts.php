<?php

namespace Goteo\Controller {

	use Goteo\Library\Text,
		Goteo\Library\Lang;

	class Texts extends \Goteo\Core\Controller {
		
		public function index ($lang = 'es') {

			$content = '';

			$using = Lang::get($lang);
			$content .= "Viendo textos en {$using->name} <hr />";
			
			$texts = Text::getAll($lang);

			foreach ($texts as $text) {
				$content .= <<<EOD
					{$text->id}: {$text->text}<br />
EOD;
			}

			echo $content;
//            include 'view/index.html.php';
		}
		
		public function translate ($lang = 'es') {

			$content = '';

			$using = Lang::get($lang);
			$content .= "Editando {$using->name} <hr />";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				$errors = array();

				foreach ($_POST as $key=>$val) {

					if (substr($key, 0, 5) == 'text_') {
						$parts = explode('_', $key);
						$data = array(
							'id' => $parts[1],
							'text' => $val,
							'lang' => $lang
						);
						
						Text::save($data, $errors);
					}
				}

				if (!empty($errors)) {
					foreach ($errors as $error) {
						$content .= '<span style="color:red;">' . $error . '</span><br />';
					}
				}
			}
			
			$texts = Text::getAll($lang);

			$content .= <<<EOD
				<form action="/texts/translate" method="post">
					<input type="hidden" name="lang" value="{$lang}" />
					<dl>
EOD;
			

			foreach ($texts as $text) {
				$id = md5($text->id);
				$content .= <<<EOD
					<dt><label for="{$id}">{$text->id}</label></dt>
					<dd><textarea id="{$id}" name="text_{$id}">{$text->text}</textarea></dd>
EOD;
			}

			$content .= <<<EOD
					</dl>
					<input type="submit" name="translate" value="Aplicar" />
				</form>
EOD;

			echo $content;
//            include 'view/index.html.php';
		}

	}
	
}