<?php

namespace Goteo\Model\Project {

    use Goteo\Model\Location;

    class ProjectLocation extends \Goteo\Core\Model {
        protected $Table = 'project_location';
        public
            $method, // latitude,longitude obtaining method
                     // ip      = auto detection from ip,
                     // browser = project automatic provided,
                     // manual    = project manually provided
            $locable = true, //if is or not locable
            $city,
            $region,
            $country,
            $country_code,  // codigo pais  ISO 3166-1 alpha-2
            $longitude,
            $latitude,
            $info, //Some stored info
            $project;

        /**
         * Recupera la geolocalización de este
         * @param varcahr(50) $project  project identifier
         * @return ProjectLocation instance
         */
	 	public static function get ($project) {

            $query = static::query("SELECT * FROM project_location WHERE project = ?", array($project));
            $prj = $query->fetchObject(__CLASS__);

            if (!$prj instanceof  \Goteo\Model\Project\ProjectLocation) {
                return false;
            }
            return $prj;
		}

		public function validate(&$errors = array()) {
            if (empty($this->project)) {
                $errors[] = 'Project ID missing!';
            }
            $methods = array('ip', 'browser', 'manual');
            if (!in_array($this->method, $methods)) {
                $errors[] = 'Method (' . $this->method . ') error! must be one of: ' . implode(', ', $methods);
            }
            if (empty($this->country_code)) {
                $errors[] = 'Country code missing';
            }
            if (empty($this->country)) {
                $errors[] = 'Country missing';
            }
            if (empty($this->longitude)) {
                $errors[] = 'Longitude missing';
            }
            if (empty($this->latitude)) {
                $errors[] = 'Latitude missing';
            }
            if (empty($errors)) {
                return true;
            }
            else {
                return false;
            }
        }

		/*
		 *  Guarda la asignación del usuario a la localización
		 */
		public function save (&$errors = array()) {
            if (!$this->validate($errors)) {
                return false;
            }

            // remove from unlocable if method is not IP
            if($this->method !== 'ip') $this->locable = true;

            $values = array(':user'         => $this->project,
                            ':method'       => $this->method,
                            ':locable'      => $this->locable,
                            ':info'         => $this->info,
                            ':city'         => $this->city,
                            ':region'       => $this->region,
                            ':country'      => $this->country,
                            ':country_code' => $this->country_code,
                            ':longitude'    => $this->longitude,
                            ':latitude'     => $this->latitude
                            );

            try {
                $sql = "REPLACE INTO project_location (project, method, locable, info, city, region, country, country_code, longitude, latitude) VALUES (:project, :method, :locable, :info, :city, :region, :country, :country_code, :longitude, :latitude)";
                self::query($sql, $values);
			} catch(\PDOException $e) {
				$errors[] = "Error updating location for project. " . $e->getMessage();
				return false;
			}
            return true;
		}

		/**
		 * Desasignar el usuario de su localización
		 *
		 * @param array $errors
		 * @return boolean
		 */
		public function delete (&$errors = array()) {
            try {
                self::query("DELETE FROM project_location WHERE project = ?", array($this->project));
            } catch(\PDOException $e) {
                $errors[] = 'Error removing location for project ' . $this->project . '. ' . $e->getMessage();
                return false;
            }
			return true;
		}

        /**
         * Adds a location to the corresponding location/project_location tables according to the project
         * @param [type] $data    [description]
         * @param array  &$errors [description]
         * @return instance of Model\Project\ProjectLocation if successfull, false otherwise
         */
        public static function addProjectLocation($data, &$errors = array()) {
            try {
                $location = new Location($data);
                if($location->save($errors)) {
                    $project_loc = new ProjectLocation(array(
                        'location' => $location->id,
                        'project' => $data['project'],
                        'method' => $data['method'],
                        'locable' => !self::isUnlocable($data['project'])
                    ));
                    if($project_loc->save($errors)) {
                        $project_loc->locations[] = $location;
                        return $project_loc;
                    }
                    if(empty($errors)) $errors[] = 'unknow error';
                }
            } catch(\PDOException $e) {
                $errors[] = "Fallo SQL ".$e->getMessage();
                return false;
            }
            return false;
        }

        /**
         * Sets a property
         * @param [type] $project    [description]
         * @param [type] $prop    [description]
         * @param [type] $value   [description]
         * @param [type] &$errors [description]
         */
        public static function setProperty($project, $prop, $value, &$errors) {
            try {
                if(self::query("INSERT INTO project_location ($prop, type, item) VALUES (:value, 'project', :project)
                                ON DUPLICATE KEY UPDATE $prop = :value", array(':value' => $value, ':project' => $project)));
                    return true;
            } catch(\PDOException $e) {
                $errors[] = 'Error modifying [' . $prop . '] with val [' . $value . '] ' . $e->getMessage();
            }
            return false;

        }


        /**
         * Borrar de unlocable
         *
         * @param varchar(50) $project id de un usuario
         * @param array $errors
         * @return boolean
         */
        public static function setLocable ($project, &$errors = array()) {
            return self::setProperty($project, 'locable', 1, $errors);
        }

        /**
         * Añadir a unlocable
         *
         * @param varchar(50) $project id de un usuario
         * @param array $errors
         * @return boolean
         */
        public static function setUnlocable ($project, &$errors = array()) {
            return self::setProperty($project, 'locable', 0, $errors);
		}


        /**
         * Si está como ilocalizable
         * @param varcahr(50) $id  project identifier
         * @return int (have an unlocable register)
         */
	 	public static function isUnlocable ($project) {

            try {
                $query = self::query("SELECT locable FROM project_location WHERE project = ?", array($project));
                return !(bool) $query->fetchColumn();
            } catch(\PDOException $e) {
                return true;
            }
		}

	}

}
