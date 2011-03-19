<?php

namespace Goteo\Model {

	use Goteo\Core\Error;
	/*
	 * Clase para sacar contenidos dinámicos para las páginas institucionales
	 * manejará el multi-idioma
	 */
    class Content extends \Goteo\Core\Model {

		public
			$title,
			$message, // text superior @FIXME 1ª aprox.
			$menu = array(), // menu especial para la pagina.. (?)
			$modules = array(), // cosas que salen en el main space de la página
			$widgets = array(), // cosas que salen en la columna lateral derecha
			$stuff; // cosas... @XXX inventos Julian

		/*
		 * @FIXME Por ahora así de estatico...
		 */
		public function __construct ($id = null, $node = null) {

			if ($id === null) {
				throw new Error(403);
			}
			// si es $node buscamos sus contenidos personalizados

			switch ($id) {
				case 'home':
					$this->title = 'Goteo.org';
					$this->message = 'Plataforma social de financiación distribuida';
					$this->modules[] = 'BASE';
				break;

				case 'dashboard':
					$this->title = 'Goteo.org';
					$this->message = 'Panel del usuario ';
					$this->menu[] = 'DASHBOARD MENU';
					$this->modules[] = 'DASHBOARD MAIN';
				break;

				case 'test':
					$this->title = 'Goteo.org';
					$this->message = '';
					$this->menu[] = 'MENU';
					$this->modules[] = 'MAIN';
					$this->widgets[] = 'SIDEBAR';
					$this->stuff = 'A piece of a stuff!';
				break;

				default:
				break;
			}
		}

		/*
		 *  Este get se usara para el panel de traducción exclusivamente
		 *  Los contenidos normales van en el panel de gestión de páginas institucionales de los nodos
		 */
		static public function get($id = null) {
			if ($id === null)
				return '';
			else
				return true;
		}

		/*
		 *   Este save se usará en el panel de traducción exclusivamente
		 *   Es para guardar contenidos específicos
		 *   Los contenidos de cada página o bien son programados o bien gestionados en la gestión de páginas
		 *
		 *
		 */
		public function save() {}

	}
    
}