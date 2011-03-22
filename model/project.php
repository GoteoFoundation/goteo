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

			// nif y telefono sin guinoes, espacios ni puntos
			if (isset($data['contract_nif'])) {
				$data['contract_nif'] = str_replace(array('_', '.', ' ', '-'), '', $data['contract_nif']);
			}
			if (isset($data['phone'])) {
				$data['phone'] = str_replace(array('_', '.', ' ', '-'), '', $data['phone']);
			}

			$fields = array(
				'contract_name',
				'contract_surname',
				'contract_nif',
				'contract_email',
				'phone',
				'address',
				'zipcode',
				'location',
				'country',
				'name',
				'image',
				'description',
				'motivation',
				'about',
				'goal',
				'related',
				'category',
				'media',
				'currently',
				'project_location',
				'resource'
				);
			
			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if (isset($data[$field])) {
					$set .= "$field = :$field, ";
					$values[":$field"] = $data[$field];
				}
			}

			if (!empty($values)) {
				$set .= "updated = :updated";
				$values[':updated'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE project SET " . $set . " WHERE id = :id";
				if (self::query($sql, $values)) {
					foreach ($fields as $field) {
						if (isset($data[$field])) {
							$this->$field = $data[$field];
						}
					}
					return true;
				} else {
					$errors[] = 'No se ha grabado correctamete. Por favor, revise los datos.';
					return false;
				}
			}
			else {
				// nada nuevo bajo el sol
				$errors[] = 'No hay ningún cambio que guardar';
				return false;
			}
		}

		/*
		 *  Para validar proyectos
		 */
		public function validate ($step, &$errors = array(), &$success = '', &$finish = false) {
			if ($step == 'overview') {
				$success = 'Enhorabuena, ha completado todos los datos del proyecto. Lo revisaremos en cuanto lo deje LISTO.';
				$finish = true;
			}
			return true;
		}

		/*
		 * Para cambiar el id temporal a idealiza
		 */
		public function rebase() {
			// idealizar el nombre
			$newid = self::checkId(self::idealiza($this->name));
			if ($newid == false) return false;
			// actualizar las tablas relacionadas
//			self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$id));
			// actualizar el registro
			if (self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id)))
				return true;
			else
				return false;

		}

		/*
		 *  Para verificar id única
		 */
		private static function checkId($id, &$num = 1) {
			if ($query = self::query("SELECT id FROM project WHERE id = :id", array(':id'=>$id))) {
				$exist = $query->fetchObject();
				// si  ya existe, cambiar las últimas letras por un número
				if ($exist->id) {
					$sufix = (string) $num;
					$take = strlen($id) - strlen($sufix);
					$id = substr($id, 0, $take) . $sufix;
					$id = self::checkId($id, $num);
				}
				return $id;
			}
			else {
				echo "Fallo en $id, $num <br />";
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