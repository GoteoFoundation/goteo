<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

/**
 * Opciones de configuración especiales para proyectos.
 * Si el proyecto no tiene una entrada en esta tabla (project_conf), se asumen valores por defecto:
 *      noinvest = 0
 *      watch = 0
 */
namespace Goteo\Model\Project {

    use Goteo\Library\Text;

    class Conf extends \Goteo\Core\Model {

        public
            $project,
            $noinvest, // no se pueden hacer más aportes
            $watch,
            $days_round1,
            $days_round2,
            $one_round,
            $help_cost,
            $help_license,
            $mincost_estimation,
            $publishing_estimation;

        /**
         * Get the conf for a project
         * @param varcahr(50) $id  Project identifier
         * @return array of configs
         */
	 	public static function get ($id) {

            try {
                $query = static::query("SELECT * FROM project_conf WHERE project = ?", array($id));
                $project_conf = $query->fetchObject(__CLASS__);

                // Valores por defecto si no existe el proyecto en la tabla
                if (!$project_conf instanceof self) {
                    $project_conf = new self;
                    $project_conf->project = $id;
                    $project_conf->noinvest = 0;
                    $project_conf->watch = 0;
                    $project_conf->days_round1 = 40;
                    $project_conf->days_round2 = 40;
                    $project_conf->one_round = 0;
                    $project_conf->help_cost = 0;
                    $project_conf->help_license = 0;
                    $project_conf->mincost_estimation=0;
                }

                return $project_conf;

            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        public function validate(&$errors = array()) {
            // TODO
            //if (!in_array($this->watch, array('0','1'))) return false;
            if (!isset($this->one_round)) $this->one_round = 0;
            if (!isset($this->help_cost)) $this->help_cost = 0;
            if (!isset($this->help_license)) $this->help_license = 0;
            //if (!in_array($this->noinvest, array('0','1'))) return false;

            return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            try {
                $sql = "REPLACE INTO project_conf (project, noinvest, watch, days_round1, days_round2, one_round, help_cost, help_license, mincost_estimation, publishing_estimation) VALUES(:project, :noinvest, :watch, :round1, :round2, :one, :helpcost, :helplicense, :mincost_estimation, :publishing_estimation)";
                $values = array(':project'=>$this->project, ':noinvest'=>$this->noinvest, ':watch'=>$this->watch,
                                ':round1'=>$this->days_round1, ':round2'=>$this->days_round2, ':one'=>$this->one_round, ':helpcost'=>$this->help_cost, ':helplicense'=>$this->help_license, ':mincost_estimation'=>$this->mincost_estimation, ':publishing_estimation'=>$this->publishing_estimation);
                return self::query($sql, $values);
            } catch(\PDOException $e) {
                $errors[] = "La configuración del proyecto no se ha guardado correctamente. Por favor, revise los datos." . $e->getMessage();
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

        /**
         * Comprobar si el proyecto es de ronda única
         *
         * @param varcahr(50) $id  Project identifier
         * @return bool
         */
        public static function isOneRound($id) {
            try {
                $query = static::query("SELECT one_round FROM project_conf WHERE project = ?", array($id));
                $watch = $query->fetchColumn();
                return ($watch == 1);
            } catch(\PDOException $e) {
                return false;
            }
        }

        /**
         * Cambiar el numero de días para que termine la ronda esta noche
         *
         * Si el proyecto está en primera ronda cambia el número de días y marca 'Ronda única'.
         * Si el proyecto está en segunda ronda cambia el númerio de días de segunda ronda teniendo en cuenta el número de días configurados en la primera.
         *
         * @param varcahr(50) $project  Project instance  from ::get()
         * @return bool
         */
        public static function finish($project) {

            $debug = false;

            try {

                // datos actuales
                if ($debug) {
                    echo "
                    Id: {$project->id}<br />
                    Ronda: {$project->round}<br />
                    Dias que le quedan: {$project->days}<br />
                    Dias que lleva: {$project->days_active}<br />
                    --<br />
                    Primera: {$project->days_round1}<br />
                    Segunda: {$project->days_round2}<br />
                    Unica: {$project->one_round}<br />
                    --<br />
                    Paso a segunda: {$project->passed}<br />
                    Pasará a segunda: {$project->willpass}<br />
                    --<br />
                    Config one round:<br />
                    ";
                    var_dump($project->round);
                    var_dump($project->one_round);
                    var_dump(is_null($project->one_round));
                    echo "<br /><br />";
                }

                // segun esté en primera o en segunda ronda
                if ($project->round === 1) {
                    // * Si el proyecto está en primera ronda cambia el número de días y marca 'Ronda única'.

                    $one_round = 1;
                    $days_round1 = $project->days_active + 1;
                    $days_round2 = $project->days_round2;

                } elseif ($project->round === 2) {
                    // * Si el proyecto está en segunda ronda cambia el númerio de días de segunda ronda
                    //        teniendo en cuenta el número de días configurados en la primera (40 por defecto al no tener configuración)

                    $one_round = 0;
                    $days_round1 = $project->days_round1;
                    $days_round2 = $project->days_active - $project->days_round1 + 1;

                } else {
                    // no está en campaña
                    if ($debug) die('No tiene ronda');
                    return false;
                }

                if ($debug) {
                    echo "NEW: <br />
                    one round {$one_round}<br />
                    days first round {$days_round1}<br />
                    days second round {$days_round2}<br />";
                }

                $values = array(':project'=>$project->id, ':round1'=>$days_round1, ':round2'=>$days_round2, ':one'=>$one_round);

                // por la configuración de ronda unica (comprobando si null) sabemos si tiene registro de configuración o no
                if (is_null($project->one_round)) {

                    // hacer un replace
                    $sql = "REPLACE INTO project_conf (project, days_round1, days_round2, one_round)
                    VALUES(:project, :round1, :round2, :one)";

                } else {

                    // hacer un update
                    $sql = "UPDATE project_conf SET days_round1 = :round1, days_round2 = :round2, one_round = :one WHERE project = :project";
                    $values = array(':project'=>$project->id, ':round1'=>$days_round1, ':round2'=>$days_round2, ':one'=>$one_round);

                }

                if ($debug) {
                    echo \sqldbg($sql, $values);
                }
                self::query($sql, $values);

                if ($debug) die;

                return true;

            } catch(\PDOException $e) {
                return false;
            }
        }

	}

}
