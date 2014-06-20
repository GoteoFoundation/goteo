<?php

/**
 * Opciones de configuración especiales para proyectos.
 * Si el proyecto no tiene una entrada en esta tabla (project_conf), se asumen valores por defecto:
 *      noinvest = 0
 *      watch = 0
 */
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
            //if (!in_array($this->noinvest, array('0','1'))) return false;

            return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_conf (project, noinvest, watch) VALUES(:project, :noinvest, :watch)";
                $values = array(':project'=>$this->project, ':noinvest'=>$this->noinvest, ':watch'=>$this->watch);
				return self::query($sql, $values);
			} catch(\PDOException $e) {
				$errors[] = "Las cuentas no se han asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

        /**
         * Cortar el grifo
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function closeInvest($id) {
            try {
                $query = "INSERT INTO project_conf (project, noinvest) VALUES (?, '1') ON DUPLICATE KEY UPDATE noinvest='1'";
                $data = array($id);
                return self::query($query, $data);
            } catch(\PDOException $e) {
                return false;
            }
        }

        /**
         * Abrir el grifo
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function openInvest($id) {
            try {
                $query = "INSERT INTO project_conf (project, noinvest) VALUES (?, '0') ON DUPLICATE KEY UPDATE noinvest='0'";
                $data = array($id);
                return self::query($query, $data);
            } catch(\PDOException $e) {
                return false;
            }
        }

        /**
         * Comprobar si el grifo está cerrado
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function isInvestClosed ($id) {
            try {
                $query = static::query("SELECT noinvest FROM project_conf WHERE project = ?", array($id));
                $conf = $query->fetchColumn();
                return ($conf == 1);
            } catch(\PDOException $e) {
                return false;
            }
        }
        
        /**
         * Vigilar un proyecto
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function watch($id) {
            try {
                $query = "INSERT INTO project_conf (project, watch) VALUES (?, '1') ON DUPLICATE KEY UPDATE watch='1'";
                $data = array($id);
                return self::query($query, $data);
            } catch(\PDOException $e) {
                return false;
            }
        }

        /**
         * Dejar de vigilar un proyecto
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function unwatch($id) {
            try {
                $query = "INSERT INTO project_conf (project, watch) VALUES (?, '0') ON DUPLICATE KEY UPDATE watch='0'";
                $data = array($id);
                return self::query($query, $data);
            } catch(\PDOException $e) {
                return false;
            }
        }

        /**
         * Comprobar si el proyecto está siendo vigilado
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
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
