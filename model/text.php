<?php

namespace Goteo\Model {

	/*
	 * Clase para sacar textos estáticos
	 * manejará el gettext para las traducciones
	 * @FIXME todo estatico a mas no poder ( y no me refiero al 'static')
	 */
    class Text extends \Goteo\Core\Model {
		
		static public function get ($id = null) {

			if ($id === null)
				return '';

			return _($id);

		}

		/*
		 *  Esto se usara para subir los .mo en el panel de gestion correspondiente
		 */
		public function save() {}

	}
    
}