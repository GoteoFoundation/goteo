<?php

namespace Goteo\Model {

    class Contract extends \Goteo\Core\Model {

        //@TODO: Hacer que el id del registrod e contrato sea el id del proyecto
        
        public
            $id,
            $project,
            $number, //numero de contrato
            $date, // día anterior a la publicación
            $fullnum, // numero+fecha de publicación
            $type,
                
            // datos del representante
            $name,
            $nif,
            $office,
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
            
            // datos de cuentas
            $bank,
            $bank_owner,
            $paypal,
            $paypal_owner,
                
            // datos de registro
            $reg_name,
            $reg_number,
            $reg_id,
                
            // proyecto
            $project_name,
            $project_url,
            $project_owner,
            $project_user,
            $project_profile,
            $project_description,
            
            // seguimiento
            $status_owner,
            $status_admin,
            $status_pdf;


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
                    SELECT *,
                        contract_status.owner as status_owner,
                        contract_status.admin as status_admin,
                        contract_status.pdf as status_pdf
                    FROM contract
                    LEFT JOIN contract_status
                        ON contract_status.contract = contract.id
                    WHERE contract.project = ?
                ";
                
                
                $query = static::query($sql, array($id));
                $contract = $query->fetchObject(__CLASS__);
                if (!empty($contract)) {
                    
                    // ponemos tambien los datos de seguimiento de estado
                    
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
                    $contract->type = (int) $projData->contract_entity; // segun si el proyecto tiene marcado persona fisica/juridica
                    
                    // persona física o representante
                    $contract->name = $projData->contract_name;
                    $contract->nif = $projData->contract_nif;
                    $contract->office = $projData->entity_office;
                    $contract->address = $projData->address;
                    $contract->location = $projData->location;
                    $contract->region = $projData->zipcode;
                    $contract->country = $projData->country;
                    
                    if ($contract->type > 0) {
                        // persona juridica
                        $contract->entity_name = $projData->entity_name;
                        $contract->entity_cif = $projData->entity_cif;
                        $contract->entity_address = $projData->secondary_address ? $projData->post_address : $projData->address;
                        $contract->entity_location = $projData->secondary_address ? $projData->post_location : $projData->location;
                        $contract->entity_region = $projData->secondary_address ? $projData->post_zipcode : $projData->zipcode;
                        $contract->entity_country = $projData->secondary_address ? $projData->post_country : $projData->country;
                    }
                    
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
                    $contract->status_owner = 0;
                    $contract->status_admin = 0;
                    $contract->status_pdf = null;
                    
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
                    project.name as project,
                    contract_status.owner as status_owner,
                    contract_status.admin as status_admin,
                    contract_status.pdf as status_pdf
                FROM contract
                INNER JOIN project
                    ON project.id = contract.project
                LEFT JOIN contract_status
                    ON contract_status.contract = contract.id
                ORDER BY project.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
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
        public function setStatus($status, $value) {
            
            $sql = "REPLACE INTO contract_status 
                SET contract = :contract,
                owner = :owner,
                admin = :admin,
                pdf = :pdf
            ";
            $values = array(
                ':contract' => $this->id,
                ':owner' => (int) $this->status_owner,
                ':admin' => (int) $this->status_admin,
                ':pdf' => (string) $this->status_pdf,
            );
            
            $values[':'.$status] = $value;
            
            if (!self::query($sql, $values)) {
                die ($sql . '<pre>' . print_r($values, 1) . '</pre>');
                return false;
            } else {
                return true;
            }
            
        }
        
        

	}
    
}