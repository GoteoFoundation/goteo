<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model;
    
    class Contract extends \Goteo\Core\Model {

        public
            $project,
            $number, //numero de contrato
            $date, // día anterior a la publicación
            $enddate, // un año después de la fecha del contrato
            $fullnum, // numero+fecha de publicación
            $type, //  0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil
                
            // datos del representante
            $name,
            $nif,
            $office, // Cargo en la asociación o empresa	
            $address,
            $location,
            $region,
            $zipcode,
            $country,
                
            // datos de la entidad
            $entity_name,
            $entity_cif,
            $entity_address,
            $entity_location,
            $entity_region,
            $entity_zipcode,
            $entity_country,
            
            // datos de cuentas (se guardan en project_account para procesos y aquí para el pdf)
            $bank,
            $bank_owner,
            $paypal,
            $paypal_owner,
                
            // datos de registro
            $reg_name,  // Registro de asociaciones o nombre del notario
            $reg_date,  // Fecha de escritura del notario
            $reg_number, // Número de registro o número de protocolo del notario
            $reg_id, // Número en el registro mercantil
                
            // proyecto
            $project_name,
            $project_url,
            $project_owner, // Id del impulsor
            $project_user, // Nombre del impulsor
            $project_profile, // URL del perfil del impulsor
                
            $project_description, // descripción del proyecto
            $project_invest, // objetivo de financiación
            $project_return, // retornos comprometidos
            
            // seguimiento (es un objeto, cada atributo es un valor de seguimiento)
            $status,
                
            // documentación
            $docs = array();


        /**
         * Creación de registro de contrato. 
         * Esto lo lanzará el cron/execute cuando el proyecto pase la primera ronda.
         * 
         * @param varchar(50) $id del proyecto
         * @return true (el control de errores habrá que hacerlo por email)
         */
        public static function create ($id, &$errors = array()) {
            $contract = new Contract;
            $contract->project = $id;
            /* sacar datos del proyecto */
            $projData = \Goteo\Model\Project::get($id, 'es');
            if (empty($contract->number) && !empty($projData->published)) {
                $date = strtotime($projData->published);
                $contract->date = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)));
                $contract->enddate = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)+1));
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

            $contract->project_name = $projData->name;
            $contract->project_url = SITE_URL . '/project/' .$projData->id;
            $contract->project_owner = $projData->owner;
            $contract->project_user = $projData->user->name;
            $contract->project_profile = SITE_URL . '/user/profile/' .$projData->owner;

            // campos de descripción del proyecto
            $contract->project_description = $projData->description;
            
            // texto montado desde costes
            $contract->project_invest = self::txtInvest($projData);
            
            // texto montado desde retornos
            $contract->project_return = self::txtReturn($projData);
            
            // cuentas
            $account = \Goteo\Model\Project\Account::get($projData->id);

            $contract->bank = $account->bank;
            $contract->bank_owner = $account->bank_owner;
            $contract->paypal = $account->paypal;
            $contract->paypal_owner = $account->paypal_owner;

            return $contract->save($errors);
        }


        
        /**
         * Datos de contrato del proyecto
         * si no hay, precargamos con los datos del proyecto
         * 
         * @param varchar(50) $id  Project identifier
         * @return instancia de contrato
         */
	 	public static function get ($id) {

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

                // cargamos los documentos
                $contract->docs = Contract\Document::getDocs($id);
            
                
                return $contract;
            } else {
                // aun no tenemos datos de contrato
                return null;
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
                    'enddate',
                    'type',
                    'name',
                    'nif',
                    'office',
                    'address',
                    'location',
                    'region',
                    'zipcode',
                    'country',
                    'entity_name',
                    'entity_cif',
                    'entity_address',
                    'entity_location',
                    'entity_region',
                    'entity_zipcode',
                    'entity_country',
                    'bank',
                    'bank_owner',
                    'paypal',
                    'paypal_owner',
                    'birthdate',
                    'reg_name',
                    'reg_date',
                    'reg_number',
                    'reg_id',
                    'project_name',
                    'project_url',
                    'project_owner',
                    'project_user',
                    'project_profile',
                    'project_description',
                    'project_invest',
                    'project_return'
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
 
        
        /*
         * comprueba los campos obligatorios
         * y los obligatorios por tipo de promotor
         * NOTA: en algunos casos usa los textos 'mandatory-' del proyecto
         */
        public function check() {
            //primero resetea los errores y los okeys
            $this->errors = self::blankErrors();
            $this->okeys  = self::blankErrors();

            $errors = &$this->errors;
            $okeys  = &$this->okeys ;

            /***************** Revisión de campos del paso PROMOTOR *****************/
            if (empty($this->name)) {
                $errors['promoter']['name'] = Text::get('mandatory-project-field-contract_name');
            } else {
                 $okeys['promoter']['name'] = 'ok';
            }

            if (empty($this->nif)) {
                $errors['promoter']['nif'] = Text::get('mandatory-project-field-contract_nif');
            } elseif (!Check::nif($this->nif) && !Check::vat($this->nif)) {
                $errors['promoter']['nif'] = Text::get('validate-project-value-contract_nif');
            } else {
                 $okeys['promoter']['nif'] = 'ok';
            }

            if (empty($this->birthdate)) {
                $errors['promoter']['birthdate'] = Text::get('mandatory-project-field-contract_birthdate');
            } else {
                 $okeys['promoter']['birthdate'] = 'ok';
            }
            
            if (empty($this->address)) {
                $errors['promoter']['address'] = Text::get('mandatory-project-field-address');
            } else {
                 $okeys['promoter']['address'] = 'ok';
            }

            if (empty($this->location)) {
                $errors['promoter']['location'] = Text::get('mandatory-project-field-residence');
            } else {
                 $okeys['promoter']['location'] = 'ok';
            }

            if (empty($this->region)) {
                $errors['promoter']['region'] = Text::get('mandatory-project-field-region');
            } else {
                 $okeys['promoter']['region'] = 'ok';
            }

            if (empty($this->zipcode)) {
                $errors['promoter']['zipcode'] = Text::get('mandatory-project-field-zipcode');
            } else {
                 $okeys['promoter']['zipcode'] = 'ok';
            }

            if (empty($this->country)) {
                $errors['promoter']['country'] = Text::get('mandatory-project-field-country');
            } else {
                 $okeys['promoter']['country'] = 'ok';
            }

            /***************** FIN Revisión del paso PROMOTOR *****************/
            
            /***************** Revisión de campos del paso ENTIDAD *****************/
            if ($this->type > 0) {  // solo obligatorios para representante
                if (empty($this->entity_name)) {
                    $errors['entity']['entity_name'] = Text::get('mandatory-project-field-entity_name');
                } else {
                     $okeys['entity']['entity_name'] = 'ok';
                }

                if (empty($this->entity_cif)) {
                    $errors['entity']['entity_cif'] = Text::get('mandatory-project-field-entity_cif');
                } elseif (!Check::nif($this->entity_cif)) {
                    $errors['entity']['entity_cif'] = Text::get('validate-project-value-entity_cif');
                } else {
                     $okeys['entity']['entity_cif'] = 'ok';
                }

                if (empty($this->office)) {
                    $errors['entity']['office'] = Text::get('mandatory-project-field-entity_office');
                } else {
                     $okeys['entity']['office'] = 'ok';
                }

                // y la dirección
                if (empty($this->entity_address)) {
                    $errors['entity']['entity_address'] = Text::get('mandatory-project-field-address');
                } else {
                     $okeys['entity']['entity_address'] = 'ok';
                }

                if (empty($this->entity_location)) {
                    $errors['entity']['entity_location'] = Text::get('mandatory-project-field-residence');
                } else {
                     $okeys['entity']['entity_location'] = 'ok';
                }

                if (empty($this->entity_region)) {
                    $errors['entity']['entity_region'] = Text::get('mandatory-project-field-region');
                } else {
                     $okeys['entity']['entity_region'] = 'ok';
                }

                if (empty($this->entity_zipcode)) {
                    $errors['entity']['entity_zipcode'] = Text::get('mandatory-project-field-zipcode');
                } else {
                     $okeys['entity']['entity_zipcode'] = 'ok';
                }

                if (empty($this->entity_country)) {
                    $errors['entity']['entity_country'] = Text::get('mandatory-project-field-country');
                } else {
                     $okeys['entity']['entity_country'] = 'ok';
                }
                
                // y los legales
                // para representantes de asociación
                if ($this->type == 1) {
                    if (empty($this->reg_name)) {
                        $errors['entity']['reg_name'] = 'Es obligatorio indicar en que registro se inscribió la asociación';
                    } else {
                         $okeys['entity']['reg_name'] = 'ok';
                    }
                    if (empty($this->reg_number)) {
                        $errors['entity']['reg_number'] = 'Es obligatorio indicar el número de registro';
                    } else {
                         $okeys['entity']['reg_number'] = 'ok';
                    }
                }

                // para representantes de entidad jurídica
                if ($this->type == 2) {
                    if (empty($this->reg_name)) {
                        $errors['entity']['reg_name'] = 'Es obligatorio indicar el nombre del notario que otorgó la escritura pública de la empresa';
                    } else {
                         $okeys['entity']['reg_name'] = 'ok';
                    }
                    if (empty($this->reg_date)) {
                        $errors['entity']['reg_date'] = 'Es obligatorio indicar la fecha en que el notario otorgó la escritura pública';
                    } else {
                         $okeys['entity']['reg_date'] = 'ok';
                    }
                    if (empty($this->reg_number)) {
                        $errors['entity']['reg_number'] = 'Es obligatorio indicar el número de protocolo del notario';
                    } else {
                         $okeys['entity']['reg_number'] = 'ok';
                    }
                    if (empty($this->reg_id)) {
                        $errors['entity']['reg_id'] = 'Es obligatorio indicar el número y la ciudad de inscripción en el Registro Mercantil';
                    } else {
                         $okeys['entity']['reg_id'] = 'ok';
                    }
                }
                
            }
            /***************** FIN Revisión del paso ENTIDAD *****************/
            
            /***************** Revisión de campos del paso CUENTAS  *****************/
            if (!empty($this->paypal) && empty($this->paypal_owner)) {
                $errors['accounts']['paypal_owner'] = 'Es obligatorio poner el nombre del titular de la cuenta PayPal del proyecto';
            } else {
                 $okeys['accounts']['paypal_owner'] = 'ok';
            }
            if (empty($this->bank)) {
                $errors['accounts']['bank'] = 'Es obligatorio indicar una cuenta bancaria para el proyecto';
            } else {
                $okeys['accounts']['bank'] = 'ok';
            }
            if (empty($this->bank_owner)) {
                $errors['accounts']['bank_owner'] = 'Es obligatorio poner el nombre del titular de la cuenta bancaria';
            } else {
                $okeys['accounts']['bank_owner'] = 'ok';
            }
            /***************** FIN Revisión del paso CUENTAS *****************/

            
            /***************** Revisión de campos del paso DOCUMENTACIÓN  *****************/
            if (empty($this->docs)) {
                $errors['documents']['docs'] = Text::get('mandatory-contract-docs');
            } else {
                $okeys['documents']['docs'] = 'ok';
            }
            /***************** FIN Revisión del paso DOCUMENTACIÓN *****************/
            
            $this->finishable = (\array_empty($errors));
        }
 
        // para guardar los fallos en los datos
        public static function blankErrors() {
            return array(
                'promoter'     => array(),
                'entity'       => array(),
                'account'      => array(),
                'documents'    => array()
            );
        }
 
        // para montar el texto de objetivo de financiación
        public static function txtInvest($projData) {
            $txt_invest_min = array();
            $txt_invest_opt = array();
            foreach ($projData->costs as $costData) {
                if ($costData->required) 
                    $txt_invest_min[] = $costData->cost;
                else
                    $txt_invest_opt[] = $costData->cost;
            }
            
            return 'El objetivo de la campaña en Goteo es financiar los costes de: 
- '
                . implode('
- ', $txt_invest_min)
                . '. 

En caso de conseguir el presupuesto óptimo, la recaudación cubriría los gastos de: 
- ' 
                . implode('
- ', $txt_invest_opt)
                . '.';
        }
        
        // para montar el texto de retornos
        public static function txtReturn($projData) {
            $licenses = array();

            foreach (Model\License::getAll() as $l) {
                $licenses[$l->id] = $l;
            }
            
            $rews = 'El retorno colectivo que ofrece el proyecto consistirá en:';
            foreach ($projData->social_rewards as $social) {
                $rews .= "

- {$social->icon_name}: {$social->reward} Será publicado bajo licencia {$licenses[$social->license]->name} ({$licenses[$social->license]->url})";
            }
            
            return $rews;
        }
        
	}
    
}