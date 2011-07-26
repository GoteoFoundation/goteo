<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception;

	/*
	 * Clase para gestionar la traducción de registros de tablas de contenido
     *
     * Ojo, todos los campos de traduccion son texto (a ver como sabemos si corto o largo...)
     *
	 */
    class Content {

        public static 
            $tables = array(
                'promote'   => 'Proyectos destacados',
                'icon'      => 'Tipos de retorno/recompensa',
                'license'   => 'Licencias',
                'category'  => 'Categorías',
                'news'      => 'Noticias',
                'faq'       => 'Faq',
                'post'      => 'Blog',
                'tag'       => 'Tags'
            ),
            $fields = array(
                'promote' => array (
                    'title' => 'Título',
                    'description' => 'Descripción'
                ),
                'icon' => array (
                    'name' => 'Nombre',
                    'description' => 'Descripción'
                ),
                'license' => array (
                    'name' => 'Nombre',
                    'description' => 'Descripción',
                    'url' => 'Enlace'
                ),
                'category' => array (
                    'name' => 'Nombre',
                    'description' => 'Descripción'
                ),
                'news' => array (
                    'title' => 'Título',
                    'description' => 'Entradilla'
                ),
                'faq' => array (
                    'title' => 'Título',
                    'description' => 'Descripción'
                ),
                'post' => array (
                    'title' => 'Título',
                    'text' => 'Texto entrada'
                ),
                'tag' => array (
                    'name' => 'Nombre'
                )
            ),
            $types = array(
                'title'       => 'Título',
                'name'        => 'Nombre',
                'description' => 'Descripción',
                'url'         => 'Enlace',
                'text'        => 'Texto extenso'
            );

        /*
         * Para sacar un registro
         */
        static public function get ($table, $id, $lang = \GOTEO_DEFAULT_LANG) {

            // buscamos el contenido para este registro de esta tabla
			$sql = "SELECT  
                        {$table}.id as id,
                        ";

            foreach (self::$fields[$table] as $field=>$fieldName) {
                $sql .= "IFNULL({$table}_lang.$field, {$table}.$field) as $field
                        ";
            }

            $sql .= "IFNULL({$table}_lang.lang, '$lang') as lang
                     FROM {$table}
                     LEFT JOIN {$table}_lang
                        ON {$table}_lang.id = {$table}.id
                        AND {$table}_lang.lang = :lang
                     WHERE {$table}.id = :id
                ";

			$query = Model::query($sql, array(
                                            ':id' => $id,
                                            ':lang' => $lang
                                        )
                                    );
			$content = $query->fetchObject(__CLASS__);
            $content->table = $table;
            
            return $content;
		}

		/*
		 *  Metodo para la lista de registros de las tablas de contenidos
		 */
		public static function getAll($filters = array(), $lang = \GOTEO_DEFAULT_LANG) {
            $contents = array();

            $values = array(':lang' => $lang);

            /// filters:  type  //tipo de campo
            //          , table //tabla o modelo o concepto
            //          , text //cadena de texto

            // si hay filtro de tabla solo sacamos de una tabla

            // si hay filtro de tipo, solo las tablas que tengan ese tipo y solo ese tipo en los resultados

            // si hay filtro de texto es para todas las sentencias

            // y todos los campos sacan el contenido "purpose" si no tienen del suyo

            $sql = "";
            $t = 0;
            foreach (self::$tables as $table=>$tableName) {
                if (!self::checkLangTable($table)) continue;
                if (!empty($filters['type']) && !isset(self::$fields[$table][$filters['type']])) continue;
                if (!empty($filters['table']) && $table != $filters['table']) continue;

                $sql .= "(SELECT
                            {$table}.id as id,
                            '{$table}' as `table`,
                            ";

                foreach (self::$fields[$table] as $field=>$fieldName) {
                    $sql .= "IFNULL({$table}_lang.$field, {$table}.$field) as $field
                            ";
                }

                $sql .= "FROM {$table}
                         LEFT JOIN {$table}_lang
                            ON {$table}_lang.id = {$table}.id
                            AND {$table}_lang.lang = :lang
                         WHERE {$table}.id 
                    ";


                // para cada campo
                if (!empty($filters['text'])) {
                    foreach (self::$fields[$table] as $field=>$fieldName) {
                        $sql .= " AND ( {$table}_lang.{$field} LIKE :text{$table}{$field} OR ({$table}_lang.{$field} IS NULL AND {$table}.{$field} LIKE :text{$table}{$field} ))
                            ";
                        $values[":text{$table}{$field}"] = "%{$filters['text']}%";
                    }
                }

                $sql .= ")
	    			UNION ALL
                    ";

                $t++;
            }

            // quito el último union si hay mas de una tabla
            if ($t > 1) {
                // $sql ....
        	    $sql = substr($sql, 0, -10);
                $sql .= " ORDER BY `table` ASC, `id' ASC";
            }

            echo $sql;
            die;

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $content) {

                    foreach (self::$fields[$content->table] as $field=>$fieldName) {
                        if (!empty($filters['type']) && $field != $filters['type']) continue;

                        $data = array(
                            'table' => $content->table,
                            'id' => $content->id,
                            'field' => $field,
                            'value' => $content->field
                        );

                        $contents[$content->table] = (object) $data;

                    }

                }
                return $contents;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

        public function validate(&$errors = array()) {
            return true;
        }

		/*
		 *  Esto se usa para actualizar datos en cualquier tabla de contenido
		 */
		public function save(&$errors = array()) {

  			try {
                // tenemos el id en $this->id  (el campo id siempre se llama id)
                // tenemos el lang en $this->lang
                // tenemos el nombre de la tabla en $this->table
                // tenemos los campos en self::$fields[$table] y el contenido de cada uno en $this->$field
                $set = '`id` = :id, `lang` = :lang ';
                $values = array(
                    ':id' => $this->id,
                    ':lang' => $this->lang
                );

                foreach (self::$fields[$this->table] as $field=>$fieldDesc) {
                    if ($set != '') $set .= ", ";
                    $set .= "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }

				$sql = "REPLACE INTO {$this->table}_lang SET $set";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido multiidioma. ' . $e->getMessage();
                return false;
			}

		}


        public static function checkLangTable($table) {
            
        }

	}
}