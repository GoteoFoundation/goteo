<?php

namespace Goteo\Model\Project {

    class Conf extends \Goteo\Core\Model {

        public
            $project,
            $noinvest, // no se pueden hacer más aportes
            $watch;


        /**
         * Get the conf for a project
         * @param varcahr(50) $id  Project identifier
         * @return array of configs
         */
	 	public static function get ($id) {

            try {
                $query = static::query("SELECT * FROM project_conf WHERE project = ?", array($id));
                $conf = $query->fetchObject(__CLASS__);
                return $conf;

            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        public function validate(&$errors = array()) {
            // TODO
            //if (!in_array($this->watch, array('0','1'))) return false;

            return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_conf (project, noinvest, watch) VALUES(:project, :noinvest, :watch)";
                $values = array(':project'=>$this->project, ':noinvest'=>$this->noinvest, ':watch'=>$this->watch);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "Las cuentas no se han asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

        // comprobar que no se le haya cerrado el grifo
        public static function getNoinvest ($id) {

            try {
                $query = static::query("SELECT noinvest FROM project_conf WHERE project = ?", array($id));
                $conf = $query->fetchColumn();
                return (!empty($conf));
            } catch(\PDOException $e) {
                return false;
            }
        }
        
        public static function watch($id) {
            try {
                //FIXME: project_conf_valores_on_demand
                $query = static::query("INSERT INTO project_conf (project, watch) VALUES (?, '1') ON DUPLICATE KEY UPDATE watch='1'", array($id));
                return $query->fetchColumn();
            } catch(\PDOException $e) {
                return false;
            }
        }

        public static function unwatch($id) {
            try {
                //FIXME: project_conf_valores_on_demand
                $query = static::query("INSERT INTO project_conf (project, watch) VALUES (?, '0') ON DUPLICATE KEY UPDATE watch='0'", array($id));
                return $query->fetchColumn();
            } catch(\PDOException $e) {
                return false;
            }
        }

        public static function isWatched($id) {
            try {
                $query = static::query("SELECT watch FROM project_conf WHERE project = ?", array($id));
                $watch = $query->fetchColumn();
                return ($watch == 1);
            } catch(\PDOException $e) {
                return false;
            }
        }

	}
    
}
