<?php

namespace Goteo\Library {

    use Goteo\Core\Model;

	/*
	 * Clase para cosas de localizaciones
	 * Para sacar las existentes, las coincidencias proyecto-usuario, las de gmaps, y geo-localizacion
	 */
    class Location {

        /**
         * Metodo para sacar las que hay en proyectos
         * @return array strings
         */
		public static function getList () {

            $results = array();

            $sql = "SELECT distinct(project_location) as location
                    FROM project
                    WHERE status > 2
                    ORDER BY location ASC";

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                    $results[md5($item->location)] = $item->location;
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la lista de localizaciones');
            }
		}

	}

}