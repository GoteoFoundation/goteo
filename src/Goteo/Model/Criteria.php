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

    use Goteo\Application\Lang;
    use Goteo\Application\Config;
    use Goteo\Library\Check,
        Goteo\Library\Text;

    class Criteria extends \Goteo\Core\Model {

        public
            $id,
            $section,
            $title,
            $description,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'criteria_lang', $lang);

                $query = static::query("
                    SELECT
                        criteria.id as id,
                        criteria.section as section,
                        IFNULL(criteria_lang.title, criteria.title) as title,
                        IFNULL(criteria_lang.description, criteria.description) as description,
                        criteria.order as `order`
                    FROM    criteria
                    LEFT JOIN criteria_lang
                        ON  criteria_lang.id = criteria.id
                        AND criteria_lang.lang = :lang
                    WHERE criteria.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));
                $criteria = $query->fetchObject(__CLASS__);

                return $criteria;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($section = 'project') {
            $lang = Lang::current();
            if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(criteria_lang.title, criteria.title) as title,
                                    IFNULL(criteria_lang.description, criteria.description) as description";
                }
                else {
                    $different_select=" IFNULL(criteria_lang.title, IFNULL(eng.title, criteria.title)) as title,
                                        IFNULL(criteria_lang.description, IFNULL(eng.description, criteria.description)) as description";
                    $eng_join=" LEFT JOIN criteria_lang as eng
                                    ON  eng.id = criteria.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            criteria.id as id,
                            criteria.section as section,
                            $different_select,
                            criteria.order as `order`
                        FROM    criteria
                        LEFT JOIN criteria_lang
                            ON  criteria_lang.id = criteria.id
                            AND criteria_lang.lang = :lang
                        $eng_join
                        WHERE criteria.section = :section
                        ORDER BY `order` ASC, title ASC";

            $query = static::query($sql, array(':section' => $section, ':lang'=>$lang));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) {
            if (empty($this->section))
                $errors[] = 'Falta seccion';
                //Text::get('mandatory-criteria-section');

            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-criteria-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'section',
                'title',
                'description',
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
                $sql = "REPLACE INTO criteria SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                $extra = array(
                    'section' => $this->section
                );
                Check::reorder($this->id, $this->move, 'criteria', 'id', 'order', $extra);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id) {
            $query = static::query("SELECT section FROM criteria WHERE id = ?", array($id));
            $criteria = $query->fetchObject();
            $extra = array(
                'section' => $criteria->section
            );
            return Check::reorder($id, 'up', 'criteria', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            $query = static::query("SELECT section FROM criteria WHERE id = ?", array($id));
            $criteria = $query->fetchObject();
            $extra = array(
                'section' => $criteria->section
            );
            return Check::reorder($id, 'down', 'criteria', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($section = 'project') {
            $query = self::query('SELECT MAX(`order`) FROM criteria WHERE section = :section'
                , array(':section'=>$section));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'project' => Text::get('criteria-project-section-header'),
                'owner' => Text::get('criteria-owner-section-header'),
                'reward' => Text::get('criteria-reward-section-header')
            );
        }


    }

}
