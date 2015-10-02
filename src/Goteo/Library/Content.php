<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
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
                'stories'   => 'Historias exitosas',
                'promote'   => 'Proyectos destacados',
                'patron'    => 'Proyectos apadrinados',
                'icon'      => 'Tipos de retorno/recompensa',
                'license'   => 'Licencias',
                'category'  => 'Categorías',
                'open_tag'  => 'Agrupaciones',
                'news'      => 'Noticias',
                'faq'       => 'Faq',
                'post'      => 'Blog',
                'banner'    => 'Banners',
                'bazar'     => 'Catalogo',
                'tag'       => 'Tags',
                'criteria'  => 'Criterios de evaluación',
                'worthcracy'=> 'Meritocrácia',
                'template'  => 'Plantillas emails automáticos',
                'glossary'  => 'Glosario de términos',
                'info'      => 'Ideas de about'
            ),
            $fields = array(
                'banner' => array (
                    'title' => 'Título',
                    'description' => 'Descripción'
                ),
                'bazar' => array (
                    'title' => 'Título',
                    'description' => 'Descripción'
                ),
                'promote' => array (
                    'title' => 'Título',
                    'description' => 'Descripción'
                ),
                'patron' => array (
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
                'open_tag' => array (
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
                    'text' => 'Texto entrada',
                    'legend' => 'Leyenda media'
                ),
                'tag' => array (
                    'name' => 'Nombre'
                ),
                'criteria' => array (
                    'title' => 'Título'
                ),
                'worthcracy' => array (
                    'name' => 'Nombre'
                ),
                'template' => array (
                    'title' => 'Título',
                    'text' => 'Contenido'
                ),
                'stories' => array (
                    'title' => 'Título',
                    'description' => 'Descripción',
                    'review' => 'Review'
                ),
                'glossary' => array (
                    'title' => 'Título',
                    'text' => 'Contenido',
                    'legend' => 'Leyenda media'
                ),
                'info' => array (
                    'title' => 'Título',
                    'text' => 'Contenido',
                    'legend' => 'Leyenda media',
                    'share_facebook' => 'Share facebook',
                    'share_twitter' => 'Share twitter'
                )
            ),
            $types = array(
                'description' => 'Descripción',
                'url'         => 'Enlace',
                'name'        => 'Nombre',
                'text'        => 'Texto extenso',
                'legend'      => 'Leyenda',
                'title'       => 'Título'
            );

        /*
         * Para sacar un registro
         */
        static public function get ($table, $id, $lang = 'original') {

            // buscamos el contenido para este registro de esta tabla
			$sql = "SELECT  
                        {$table}.id as id,
                        ";

            if ($table == 'post') {
                $sql .= "{$table}.blog as blog,
                        ";
            }

            foreach (self::$fields[$table] as $field=>$fieldName) {
                $sql .= "IFNULL({$table}_lang.$field, {$table}.$field) as $field,
                         {$table}.$field as original_$field,
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
		public static function getAll($filters = array(), $lang = 'original') {
            $contents = array(
                'ready' => array(),
                'pending' => array()
            );

            /// filters:  type  //tipo de campo
            //          , table //tabla o modelo o concepto
            //          , text //cadena de texto

            // si hay filtro de tabla solo sacamos de una tabla

            // si hay filtro de tipo, solo las tablas que tengan ese tipo y solo ese tipo en los resultados

            // si hay filtro de texto es para todas las sentencias

            // y todos los campos sacan el contenido "purpose" si no tienen del suyo

            try {

                \asort(self::$tables);
                
                foreach (self::$tables as $table=>$tableName) {
                    if (!self::checkLangTable($table)) continue;
                    if (!empty($filters['type']) && !isset(self::$fields[$table][$filters['type']])) continue;
                    if (!empty($filters['table']) && $table != $filters['table']) continue;

                    $sql = "";
                    $primercampo = "";
                    $values = array();

                    $sql .= "SELECT
                                {$table}.id as id,
                                ";

                    foreach (self::$fields[$table] as $field=>$fieldName) {
                        $sql .= "IFNULL({$table}_lang.$field, {$table}.$field) as $field,
                                {$table}.$field as {$field}_original,
                                IF({$table}_lang.$field IS NULL OR {$table}_lang.pending = 1, 0, 1) as {$field}ready,
                                ";
                        $primercampo = ($primercampo == '') ?: "{$field}ready";
                    }

                    $sql .= "CONCAT('{$table}') as `table`
                            ";

                    $sql .= "FROM {$table}
                             LEFT JOIN {$table}_lang
                                ON {$table}_lang.id = {$table}.id
                                AND {$table}_lang.lang = '$lang'
                             WHERE {$table}.id IS NOT NULL
                        ";

                        // solo entradas de goteo en esta gestión
                        if ($table == 'post') {
                            $sql .= "AND post.blog = 1
                                ";
                        }
                        if ($table == 'info') {
                            $sql .= "AND info.node = '".\GOTEO_NODE."'
                                ";
                        }

                    // para cada campo
                        $and = "AND";
                    if (!empty($filters['text'])) {
                        foreach (self::$fields[$table] as $field=>$fieldName) {
                            $sql .= " $and ( {$table}_lang.{$field} LIKE :text{$field} OR ({$table}_lang.{$field} IS NULL AND {$table}.{$field} LIKE :text{$field} ))
                                ";
                            $values[":text{$field}"] = "%{$filters['text']}%";
                            $and = "OR";
                        }
                    }

                    // ojo originales vacios
                    foreach (self::$fields[$table] as $field=>$fieldName) {
                        $sql .= " AND {$table}.{$field} IS NOT NULL
                            ";
                    }

                    // pendientes de traducir (sin valor o con campo pending)
                    if (!empty($filters['pending'])) {
                        $sql .= " HAVING $primercampo = 0";
                    }

                    $sql .= " ORDER BY id ASC";

                    /*
                    echo $sql . '<br /><br />';
                    var_dump($values);
                    echo '<br /><br />';
                     *
                     */

                    $query = Model::query($sql, $values);
                    foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $content) {

                        foreach (self::$fields[$table] as $field=>$fieldName) {
                            if (!empty($filters['type']) && $field != $filters['type']) continue;

                            $origField = "{$field}_original";

                            $data = array(
                                'table' => $table,
                                'tableName' => $tableName,
                                'id' => $content->id,
                                'field' => $field,
                                'fieldName' => $fieldName,
                                'value' => $content->$field,
                                'original' => $content->$origField
                            );

                            $campoready = $field . 'ready';

                            $group = $content->$campoready == 1 ? 'ready' : 'pending';

                            $contents[$group][] = (object) $data;

                        }

                    }

                }

                return $contents;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
            }
		}

        public function validate(&$errors = array()) {
            return true;
        }

		/*
		 *  Esto se usa para actualizar datos en cualquier tabla de contenido
		 */
		public static function save($data, &$errors = array()) {

            if (empty($data)) {
                $errors[] = "Sin datos";
                return false;
            }
            if (empty($data['lang']) || $data['lang'] == 'original') {
                $errors[] = "No se peude traducir el contenido original, seleccionar un idioma para traducir";
                return false;
            }

  			try {
                // tenemos el id en $this->id  (el campo id siempre se llama id)
                // tenemos el lang en $this->lang
                // tenemos el nombre de la tabla en $this->table
                // tenemos los campos en self::$fields[$table] y el contenido de cada uno en $this->$field

                $set = '`id` = :id, `lang` = :lang ';
                $values = array(
                    ':id' => $data['id'],
                    ':lang' => $data['lang']
                );

                if ($data['table'] == 'post') {
                    $set .= ', `blog` = :blog';
                    $values[':blog'] = $data['blog'];
                }

                foreach (self::$fields[$data['table']] as $field=>$fieldDesc) {
                    if ($set != '') $set .= ", ";
                    $set .= "`$field` = :$field ";
                    $values[":$field"] = $data[$field];
                }

				$sql = "REPLACE INTO {$data['table']}_lang SET $set";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido multiidioma. ' . $e->getMessage();
                return false;
			}

		}


        public static function checkLangTable($table) {
            //assume yes
            return true;
        }

	}
}
