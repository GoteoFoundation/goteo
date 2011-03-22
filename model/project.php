<?php

namespace Goteo\Model {
    
    class Project extends \Goteo\Core\Model {
        
        public        
            // Node this project belongs to
            $node,
        
            // Description
            $id,
            $name,
            $image,
            $description,
            $motivations,
            $about,
            $goals,
            $categories = array(),
            $media,
            $keywords = array(),
            $status,
            $location,
                
            // Tasks
            $tasks,
            $schedule,
            
            // Rewards
            $rewards;
        

		/*
		 *  Cargamos los datos del usuario al crear la instancia
		 *  @TODO: tener en cuenta la diferencia entre querer cargar un proyecto que no existe y querer crear un proyecto
		 */
		public function __construct($id = null) {
			if ($id != null) {
				$fields = self::get($id);
				
				// metemos los datos del proyecto en la instancia
				foreach ($fields as $data=>$value) {
					if (property_exists($this, $data) && !empty($value)) {
						$this->$data = $value;
					}
				}
			}
		}

		/**
		 * Coge todos los campos de la tabla
		 * @param string $id
		 */
		public static function get ($id) {
			$query = self::query("SELECT * FROM project WHERE id = :id", array(':id' => $id));
			return $query->fetchObject();
		}

		/**
		 * Saca una lista de proyectos
		 * @TODO: filtros
		 */
		public static function getAll($filters = array()) {
			$vals = array();
			$filter = "";
			foreach ($filters as $field=>$value) {
				$filter .= $filter == "" ? " WHERE" : " AND";
				$filter .= " $field = ?";
				$vals[] = $value;
			}
			$query = self::query("SELECT * FROM project" . $filter, $vals);
			return $query->fetchAll();
		}


		/**
		 * Inserta un proyecto con los datos mínimos
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function create($data = array()) {
			if (empty($data) ||
				empty($data['user']) ||
				empty($data['node']) ||
				empty($data['name'])) {
					return false;
				}

			$values = array(
				':id'	=> self::idealiza($data['name']),
				':owner' => $data['user'],
				':name'	=> $data['name'],
				':node' => $data['node'],
				':created'	=> date('Y-m-d'),
				':status'	=> 1
				);

			$sql = "INSERT INTO project (id, user, name, email, password, signup, active)
				 VALUES (:id, :user, :name, :email, :password, :signup, :active)";
			if (self::query($sql, $values)) {
				$this->id = $values[':id'];

				// cargar los datos legales del usuario

				return true;
			} else {
				echo "ERROR $sql<br /><pre>" . print_r($values, 1) . "</pre>";
				return false;
			}
		}


		/**
		 * actualiza en un proyecto pares de campo=>valor
		 * @param array $data
		 * @param array $errors
		 */
		public function save ($data, &$errors = array()) {
			if (empty($data)) {
					$errors[] = 'Datos insuficientes';
					return false;
				}

		}

    }
    
}