<?php

namespace Goteo\Model {

    class Contract extends \Goteo\Core\Model {

        public
            $project,
            $number, //numero de contrato
            $date, // día anterior a la publicación
            $fullnum, // numero+fecha de publicación
            $type, //  0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil
                
            // datos del representante
            $name,
            $nif,
            $office, // Cargo en la asociación o empresa	
            $address,
            $location,
            $region,
            $country,
                
            // datos de la entidad
            $entity_name,
            $entity_cif,
            $entity_address,
            $entity_location,
            $entity_region,
            $entity_country,
            
            // datos de cuentas (se guardan en project_account para procesos y aquí para el pdf)
            $bank,
            $bank_owner,
            $paypal,
            $paypal_owner,
                
            // datos de registro
            $reg_name,  // Registro de asociaciones o nombre del notario
            $reg_number, // Número de registro o número de protocolo del notario
            $reg_id, // Número en el registro mercantil
                
            // proyecto
            $project_name,
            $project_url,
            $project_owner, // Id del impulsor
            $project_user, // Nombre del impulsor
            $project_profile, // URL del perfil del impulsor
            $project_description,
            
            // seguimiento (es un objeto, cada atributo es un valor de seguimiento)
            $status;


        /**
         * Datos de contrato del proyecto
         * si no hay, precargamos con los datos del proyecto
         * 
         * @param varchar(50) $id  Project identifier
         * @return instancia de contrato
         */
	 	public static function get ($id) {

            try {
                $sql = "
                    SELECT *
                    FROM contract
                    WHERE contract.project = ?
                ";
                
                
                $query = static::query($sql, array($id));
                $contract = $query->fetchObject(__CLASS__);
                if (!empty($contract)) {
                    
                    // ponemos tambien los datos de seguimiento de estado
                    $contract->status = self::getStatus($id);
                    
                    return $contract;
                } else {
                    $contract = new Contract();
                    $contract->project = $id;
                    /* sacar datos del proyecto */
                    $projData = \Goteo\Model\Project::get($id);
                    if (empty($contract->number) && !empty($projData->published)) {
                        $date = strtotime($projData->published);
                        $contract->date = date('dmY', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)));
                    }
                    $contract->type = 0; // inicialmente persona fisica
                    
                    // @TODO como ya no tendremos paso 2, estos datos se inicializan con los datos personales del impulsor
                    $personalData = \Goteo\Model\User::getPersonal($projData->owner);
                    
                    // persona física o representante
                    $contract->name = $personalData->contract_name;
                    $contract->nif = $personalData->contract_nif;
                    $contract->address = $personalData->address;
                    $contract->location = $personalData->location;
                    $contract->region = '';
                    $contract->country = $personalData->country;
                    
                    $contract->project_description = $projData->description;
                    $contract->project_name = $projData->name;
                    $contract->project_url = SITE_URL . '/project/' .$projData->id;
                    $contract->project_owner = $projData->owner;
                    $contract->project_user = $projData->user->name;
                    $contract->project_profile = SITE_URL . '/user/profile/' .$projData->owner;
                    
                    // cuentas
                    $account = \Goteo\Model\Project\Account::get($projData->id);
                    
                    $contract->bank = $account->bank;
                    $contract->bank_owner = $account->bank_owner;
                    $contract->paypal = $account->paypal;
                    $contract->paypal_owner = $account->paypal_owner;
                    
                    // datos de seguimiento vacios
                    $contract->status = new \stdClass();
                    
                    return $contract;
                }
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->project)) {
                $errors[] = 'No hay ningun proyecto con el que relacionar el contrato';
                return false;
            }

            return true;
        }

        
        /*
         * Segun sie s una grabación parcial de impulsor o una grabación completa de admin
         * 
         */
		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
                
                $fields = array(
                    'project',
                    'number',
                    'date',
                    'type',
                    'name',
                    'nif',
                    'office',
                    'address',
                    'location',
                    'region',
                    'country',
                    'entity_name',
                    'entity_cif',
                    'entity_address',
                    'entity_location',
                    'entity_region',
                    'entity_country',
                    'bank',
                    'bank_owner',
                    'paypal',
                    'paypal_owner',
                    'reg_name',
                    'reg_number',
                    'reg_id',
                    'project_name',
                    'project_url',
                    'project_owner',
                    'project_user',
                    'project_profile',
                    'project_description'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$field;
                }

				$sql = "REPLACE INTO contract SET " . $set;
				if (!self::query($sql, $values)) {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    return false;
                } else {
                    return true;
                }
                
			} catch(\PDOException $e) {
				$errors[] = "Los datos de contrato no se han gaurdado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}
        
        /*
         * Lista de contratos existentes para gestión
         */
	 	public static function getAll () {

            $list = array();

            $query = static::query("
                SELECT
                    project.id as id,
                    contract.number as number,
                    project.name as project
                FROM contract
                INNER JOIN project
                    ON project.id = contract.project
                ORDER BY project.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $item->status = self::getStatus($item->id);
                $list[$item->id] = $item;
            }

            return $list;
        }
        
        
        /*
         * Lista de Proyectos que han rellenado algo del contrato
         */
	 	public static function getProjects () {

            $list = array();

            $query = static::query("
                SELECT
                    project.id,
                    project.name
                FROM project
                INNER JOIN contract
                    ON project.id = contract.project
                ORDER BY project.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }
        
        
        /* Otros métodos para seguimiento de estado de contrato */
        
        /*
         * Obtener estado de contrato
         */
	 	public static function getStatus ($id) {

            $query = static::query("
                SELECT *
                FROM contract_status 
                WHERE contract_status.contract = ?
                ", array($id));

            $status = $query->fetchObject();

            return $status;
        }
        
        /**
         * Metodo para aplicar cambios al estado del contrato
         * @param varchar(50) $id del proyecto
         * @param array $statuses array asociativo: campo => valor a modificar
         * @return bool si se ejecuta la sentencia o no
         */
        public static function setStatus($id, $statuses) {
            
            $fields = array();
            $values = array();

            // verificamos registro
            $query = static::query("SELECT contract FROM contract_status WHERE contract_status.contract = ?", array($id));

            $regExist = $query->fetchColumn();
            if (empty($regExist)) {
                $sql = "REPLACE INTO";
                $statuses['contract'] = $id;
                $sqlend = '';
            } else {
                $sql = "UPDATE";
                $sqlend = " WHERE contract = :id";
                $values[':id'] = $id;
            }
            
            foreach ($statuses as $key => $value) {
                $fields[] = "{$key} = :{$key}";
                $values[":{$key}"] = $value;
            }
            
            $sql .= " contract_status SET ";
            $sql .= implode(', ', $fields);
            $sql .= $sqlend;

            return (static::query($sql, $values)) ? true : false;
        }
        

        /* Otros métodos para control desde admin  (además de cambios de estado)
         * 
         * - set number
         * - refresh date
         * 
         */
 
        
	}
    
}