<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace GoteoBot\Model {

    use Goteo\Library\Text;

    class ProjectBot extends \Goteo\Core\Model {

        protected $Table = 'project_bot';
        protected static $Table_static = 'project_bot';    

        const TELEGRAM = "telegram";

        public
            $project,
            $platform,
            $channel_id;

        public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->project))
                $errors[] = 'No hay ningun proyecto al que asignar';
    
            if (empty($this->platform))
            $errors[] = 'No hay ninguna plataforma asignada';

            if (empty($this->channel_id))
            $errors[] = 'No hay ningun canal asignado';

            // Any of this errors makes the validation fail

            if (!empty($errors))
                return false;
            else
                return true;
        }
        

        public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM project_bot WHERE project = :id", array(':id' => $id));
                return $query->fetchAll(\PDO::FETCH_OBJ);
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        public function save (&$errors = array()) {

            if (!$this->validate($errors)) {
                return false;
            }
    
            $fields = array(
                'project',
                'platform',
                'channel_id',
                );
    
            try {
                //automatic $this->id assignation
                $this->dbInsertUpdate($fields);
                return true;

            } catch(\PDOException $e) {
                $errors[] = "ProjectBot save error: " . $e->getMessage();
                return false;
            }
        }
    
    }

}
