<?php

namespace Goteo\Model {
    
    class Project extends \Goteo\Core\Model {
        
        public        
            $id,
			$owner, // User who created it
            $node, // Node this project belongs to
			$status,
			$progress,
			$amount, // Current donated amount

			// Register contract data
			$contract_name,
			$contract_surname,
			$contract_nif, // Guardar sin espacios ni puntos ni guiones
			$contract_email,
			$phone, // guardar sin espacios ni puntos
			$address,
			$zipcode,
			$location, // owner's location
			$country,

            // Edit project description
            $name,
            $image,
            $description,
             $motivation,
             $about,
             $goal,
			 $related,
            $category,
            $media,
            $keywords = array(), // related to the project category
            $currently, // Current development status of the project
            $project_location, // project execution location
                
            // Tasks
            $tasks = array(),  // project\task instances with type
            $schedule, // picture of the tasks schedule
			$resource, // other current resources
            
            // Rewards
            $social_rewards = array(), // instances of project\reward for the public (collective type)
            $invest_rewards = array(), // instances of project\reward for investors  (individual type)

			// Collaborations
			$supports = array(); // instances of project\support
        

		/*
		 *  Cargamos los datos del usuario al crear la instancia
		 *  @TODO: tener en cuenta la diferencia entre querer cargar un proyecto que no existe y querer crear un proyecto
		 */
		public function __construct($id = null) {
			if ($id != null) {
				if ($fields = self::get($id)) {
					// metemos los datos del proyecto en la instancia
					foreach ($fields as $data=>$value) {
						if (property_exists($this, $data) && !empty($value)) {
							$this->$data = $value;
						}
					}
				}
				else {
					echo 'Fallo al crear la instancia de Project<br />';
				}
			}
		}


		/**
		 * Inserta un proyecto con los datos mínimos
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function create($user = null, $node = 'goteo') {
			if ($user == null) {
					return false;
				}

			// cojemos el número de proyecto de este usuario
			$query = self::query("SELECT COUNT(id) as num FROM project WHERE owner = ?", array($user));
			$now = $query->fetchObject();
			$num = $now->num + 1;

			$values = array(
				':id'	=> md5($user.'-'.$num),
				':name'	=> $num,
				':status'	=> 1,
				':progress'	=> 0,
				':owner' => $user,
				':node' => $node,
				':amount' => 0,
				':created'	=> date('Y-m-d')
				);

			$sql = "INSERT INTO project (id, name, status, progress, owner, node, amount, created)
				 VALUES (:id, :name, :status, :progress, :owner, :node, :amount, :created)";
			if (self::query($sql, $values)) {
				$this->id = $values[':id'];
				$this->owner = $user;
				$this->node = $node;
				$this->status = 1;
				$this->progress = 0;

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

		/*
		 * Lista de proyectos de un usuario
		 */
		public static function ofmine($owner = null)
		{
			$filters = array('owner'=>$owner);
			$projects = self::getAll($filters, 'name ASC');
			$list = array();
			foreach ($projects as $proj) {
				$list[] = (object) array('id'=>$proj['id'], 'name'=>$proj['name']);
			}
			return $list;
		}




		/**
		 * Coge todos los campos de la tabla
		 * @param string $id
		 */
		public static function get ($id) {
			if ($query = self::query("SELECT * FROM project WHERE id = ?", array($id))) {
				return $query->fetchObject();
			}
			else {
				return false;
			}
		}

		/**
		 * Saca una lista de proyectos
		 * @TODO: filtros
		 */
		public static function getAll($filters = array(), $order = '') {
			$vals = array();
			$filter = "";
			foreach ($filters as $field=>$value) {
				$filter .= $filter == "" ? " WHERE" : " AND";
				$filter .= " $field = ?";
				$vals[] = $value;
			}

			if (!empty ($order)) {
				$order = " ORDER BY $order";
			}

			$query = self::query("SELECT * FROM project" . $filter . $order, $vals);
			return $query->fetchAll();
		}

    }

}