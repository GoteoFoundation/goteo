<?php

namespace Goteo\Library {

    use Goteo\Core\Model,
        Goteo\Model\Project;

	/*
	 * Clase para realizar búsquedas de proyectos
	 *
	 */
    class Search {

        /**
         * Metodo para buscar un textxto entre todos los contenidos de texto de un proyecto
         * @param string $value
         * @return array results
         */
		public static function text ($value) {

            $results = array();

            $values = array(':text'=>"%$value%");

            $sql = "SELECT id 
                    FROM project
                    WHERE name LIKE :text
                    OR description LIKE :text
                    OR motivation LIKE :text
                    OR about LIKE :text
                    OR goal LIKE :text
                    OR related LIKE :text
                    OR keywords LIKE :text
                    OR location LIKE :text
                    ORDER BY name ASC";

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    $results[] = Project::get($match->id);
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
		}

	}

}