<?php

namespace Goteo\Project {

    class Support extends \Goteo\Core\Model {

        public
            $num,
			$project,
			$name;

		/*
		 *  Devuelve la lista de keywords de un proyecto
		 */
		public static function get ($project) {
			$list = array();

			if ($query = self::query("SELECT id, name FROM support WHERE project = ?", array($project))) {
				$keys = $query->fetchAll();
				foreach ($keys as $key) {
					$list[] = (object) array('id'=>$key['id'], 'name'=>$key['name']);
				}
			}

			return $list;
		}

		public function save ($data, &$errors) {
			echo 'Save support <pre>' . print_r($data, 1) . '</pre>';
		}

		public static function create ($project, $data, &$errors) {
			echo 'New support <pre>' . print_r($data, 1) . '</pre>';
		}

	}

}