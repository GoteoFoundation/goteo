<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model {

    use Goteo\Library\Text;
    use Goteo\Application\Lang;

    class Icon extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $order,
            $group,  // agrupación de iconos 'social' = Retornos colectivos    'individual' = Recompensas individuales
            $licenses; // licencias relacionadas con este tipo de retorno (solo para retornos colectivos)

        /*
         *  Devuelve datos de un icono
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'icon_lang', $lang);

                $query = static::query("
                    SELECT
                        icon.id as id,
                        IFNULL(icon_lang.name, icon.name) as name,
                        IFNULL(icon_lang.description, icon.description) as description,
                        icon.group as `group`,
                        icon.order as `order`
                    FROM    icon
                    LEFT JOIN  icon_lang
                        ON  icon_lang.id = icon.id
                        AND icon_lang.lang = :lang
                    WHERE icon.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));
                $icon = $query->fetchObject(__CLASS__);

                return $icon;
        }

        /*
         * Lista de iconos de recompensa
         */
        public static function getAll ($group = '') {

            $values = array(':lang'=>Lang::current());

            $icons = array();

            if(self::default_lang(Lang::current())=='es') {
                $different_select=" IFNULL(icon_lang.name, icon.name) as name,
                                    IFNULL(icon_lang.description, icon.description) as description";
            }
            else {
                $different_select=" IFNULL(icon_lang.name, IFNULL(eng.name, icon.name)) as name,
                                    IFNULL(icon_lang.description, IFNULL(eng.description, icon.description)) as description";
                $eng_join=" LEFT JOIN  icon_lang as eng
                            ON  eng.id = icon.id
                            AND eng.lang = 'en'";
            }

            $sql="SELECT
                    icon.id as id,
                    icon.group as `group`,
                    $different_select
                    FROM icon
                    LEFT JOIN  icon_lang
                        ON  icon_lang.id = icon.id
                        AND icon_lang.lang = :lang
                    $eng_join";

            if ($group != '') {
                // de un grupo o de todos
                $sql .= " WHERE icon.group = :group OR icon.group IS NULL OR icon.group = ''";
                $values[':group'] = $group;
            }

            $sql .= " ORDER BY `order` ASC, name ASC";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $icon) {
                if ($group == 'social') {
                    $icon->licenses = License::getAll($icon->id);
                }
                $icons[$icon->id] = $icon;
            }

            return $icons;
        }

        /*
         * Lista de iconos que se usen en proyectos
         */
        public static function getList ($group = '') {

            $values = array(':lang'=>Lang::current());

            $icons = array();

            if(self::default_lang(Lang::current())=='es') {
                $different_select=" IFNULL(icon_lang.name, icon.name) as name";
            }
            else {
                $different_select=" IFNULL(icon_lang.name, IFNULL(eng.name, icon.name)) as name";
                $eng_join=" LEFT JOIN  icon_lang as eng
                            ON  eng.id = icon.id
                            AND eng.lang = 'en'";
            }

            $sql="SELECT
                    icon.id,
                    $different_select
                FROM    icon
                LEFT JOIN  icon_lang
                    ON  icon_lang.id = icon.id
                    AND icon_lang.lang = :lang
                $eng_join
                INNER JOIN reward
                    ON icon.id = reward.icon";

            if ($group != '') {
                // de un grupo o de todos
                $sql .= " WHERE icon.group = :group OR icon.group IS NULL OR icon.group = ''";
                $values[':group'] = $group;
            }

            $sql .= "
                GROUP BY icon.id
                ORDER BY icon.name ASC
                ";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $icon) {
                $icons[$icon->id] = $icon;
            }

            return $icons;
        }

        public function validate (&$errors = array()) {
            if (empty($this->name))
                $errors[] = 'Falta nombre';
                //Text::get('mandatory-icon-name');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            if (empty($this->group)) {
                $this->group = null;
            }

            $fields = array(
                'id',
                'name',
                'description',
                'group',
                'order'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO icon SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        public static function groups () {
            return array(
                'social' => 'Retorno colectivo',
                'individual' => 'Recompensa individual'
            );
        }


    }

}
