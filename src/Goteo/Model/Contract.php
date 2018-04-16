<?php

namespace Goteo\Model;

use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\License;

class Contract extends \Goteo\Core\Model {

    public
        $project,
        $number, //numero de contrato
        $date, // día anterior a la publicación
        $enddate, // un año después de la fecha del contrato
        $pdf, // si está generado aquí viene el nomre de archivo en
        $type, //  0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil

        // datos del representante
        $name,
        $nif,
        $birthdate,
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
        $reg_name,  // Nombre del registro en el que está incrita la entidad (nombre completo y ciudad)
        $reg_number, // Número de registro
        $reg_date,  // Fecha de escritura del notario
        $reg_id, // Número en el registro mercantil
        $reg_idname, // Nombre del notario
        $reg_idloc, // Ciudad de actuacióndel notario

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
     * Sobrecarga de métodos 'getter'.
     *
     * @param type string $name
     * @return type mixed
     */

    public function __get($name) {
        switch ($name) {
            case "fullnum":
                //num-00000000
                return $this->number.'-'.$this->txtdate;
                break;
            default:
                return $this->$name;
        }
    }

    /**
     * Creación de registro de contrato.
     * Esto lo lanzará el cron/execute cuando el proyecto pase la primera ronda.
     *
     * Pero también se puede crear manualmente desde panel gestoria
     *   hay que tener en cuenta si ya tiene registro de contrato o no
     *
     * @param varchar(50) $id del proyecto
     * @return true (el control de errores habrá que hacerlo por email)
     */
    public static function create ($id, &$errors = array()) {

        $contract = Contract::get($id);

        if (!empty($contract)) {
            // ya tenemos registro de contrato

            // verificar fechas
            if ( empty($contract->date) || empty($contract->enddate) ) {

                // sacar datos del proyecto
                $projData = Project::get($id, 'es');
                if ( !empty($projData->published) ) {
                    $date = strtotime($projData->published);
                    $contract->date = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)));
                    $contract->enddate = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)+1));
                }

                return $contract->save($errors);

            } else {

                return $contract;

            }

        } else {
            // nuevo registro
            $contract = new Contract;
            $contract->project = $id;
            // sacar datos del proyecto
            $projData = Project::get($id, 'es');

            if ( !empty($projData->published) ) {
                $date = strtotime($projData->published);
                $contract->date = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)));
                $contract->enddate = date('Y-m-d', mktime(0, 0, 0, date('m', $date), date('d',$date)-1, date('Y', $date)+1));
            }

        }

        $contract->type = 0; // inicialmente persona fisica

        // @FIXME esto tendria que venir de lo rellenado en el paso 2 del formulario de proyecto
        $personalData = User::getPersonal($projData->owner);
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
        $account = Project\Account::get($projData->id);

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
            SELECT *, DATE_FORMAT(date, '%d%m%Y') as txtdate
            FROM contract
            WHERE contract.project = ?
        ";

        $query = static::query($sql, array($id));
        $contract = $query->fetchObject(__CLASS__);
        if (!empty($contract)) {

            // ponemos tambien los datos de seguimiento de estado
            $contract->status = self::getStatus($id);

            // si no tiene flag de "listo para imprimir" solo lo mostramos y como borrador
            $contract->draft = ($contract->status->ready) ? false : true;


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

    /**
     * Gets the % of the filled project. 100% means it can be published
     * @return stdClass Object with parts and globals percents
     */
    public function getValidation() {
        $res = new \stdClass;
        $errors =  $fields = ['promoter' => [], 'entity' => [], 'accounts' => [], 'documents' => []];


        // 1. promoter
        $promoter = [ 'name', 'nif', 'address', 'location', 'region', 'zipcode', 'country' ];
        $total = count($promoter);
        $count = 0;
        foreach($promoter as $field) {
            if(!empty($this->{$field})) {
                continue;
            }
            $fields['promoter'][] = $field;
            $count++;
        }
        if($count > 0) {
            $errors['promoter'][] = 'promoter';
        }
        // if(!Check::nif($this->nif)) {
        //     $count++;
        //     $errors['promoter'][] = 'promoter_nif';
        // }
        $res->promoter = round(100 * ($total - $count)/$total);

        // 2. entity
        if($this->type > 0) {
            $entity = ['entity_name', 'entity_cif', 'office', 'entity_address', 'entity_location', 'entity_region', 'entity_zipcode', 'entity_country'];
            $total = count($entity);
            $entity[] = 'reg_name';
            $entity[] = 'reg_number';
            if($this->type == 2) {
                $entity[] = 'reg_date';
                $entity[] = 'reg_id';
                $entity[] = 'reg_idname';
                $entity[] = 'reg_idloc';
            }
            $count = 0;
            foreach($entity as $field) {
                if(!empty($this->{$field})) {
                    continue;
                }
                $fields['entity'][] = $field;
                $count++;
            }
            if($count > 0) {
                $errors['entity'][] = 'entity';
            }
            if(!Check::nif($this->entity_cif)) {
                $count++;
                $errors['entity'][] = 'promoter_nif';
            }

            $res->entity = round(100 * ($total - $count)/$total);
        } else {
            $res->entity = 100;
        }

        // 3. accounts
        $accounts = ['bank', 'bank_owner'];
        if ($this->paypal) {
            $accounts[] = 'paypal_owner';
        }
        $total = count($accounts);
        $count = 0;
        foreach($accounts as $field) {
            if(!empty($this->{$field})) {
                continue;
            }
            $fields['accounts'][] = $field;
            $count++;
        }
        if($count > 0) {
            $errors['accounts'][] = 'accounts';
        }
        $res->accounts = round(100 * ($total - $count)/$total);

        // 4. documents
        if(!$this->docs) {
            $errors['documents'][] = 'documents';
            $res->documents = 0;
        } else {
            $res->documents = 100;
        }
        // Summary
        $sum = $total = 0;
        foreach($res as $key => $percent) {
            $sum += (int)($percent);
            $total++;
        }
        $res->global = round($sum/$total);
        $res->errors = $errors;
        $res->fields = $fields;
        $res->project = $this->id;
        // var_dump($res);
        return $res;
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
                'reg_idloc',
                'reg_idname',
                'project_name',
                'project_url',
                'project_owner',
                'project_user',
                'project_profile',
                'project_description',
                'project_invest',
                'project_return'
            );
            // print_r((array)$this);die;
            if(static::get($this->project)) {
                $ok = $this->dbUpdate($fields, ['project']);
            } else {
                $ok = $this->dbInsert($fields);
            }
            return $ok;

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


    /*
     * Obtener numero y fecha de contrato
     */
 	public static function getNum ($id, $published = null) {

        $query = static::query("
            SELECT number, DATE_FORMAT(date, '%d%m%Y') as cdate
            FROM contract
            WHERE project = ?
            ", array($id));

        $reg = $query->fetchObject();
        if (!empty($reg->number) && !empty($reg->cdate)) {
            return array($reg->number, $reg->cdate);
        } else {
            // si no hay registro, la fecha de contrato es el día antes de la publicación del proyecto
            $dPublished = (isset($published)) ? strtotime($published) : strtotime(date('dmY'));
            $date = date('dmY', mktime(0, 0, 0, date('m', $dPublished), date('d', $dPublished)-1, date('Y', $dPublished)));
            $num = 'Num';
            return array($num, $date);
        }
    }

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
    public static function setStatus($id, $statuses, User $user) {

        $fields = array();
        $values = array();

        // verificamos registro
        $query = static::query("SELECT contract FROM contract_status WHERE contract_status.contract = ?", $id);

        $regExist = $query->fetchColumn();
        if (empty($regExist)) {
            $sql = "REPLACE INTO";
            $sqlend = '';
            $fields[] = "contract = :id";
            $values[':id'] = $id;
        } else {
            $sql = "UPDATE";
            $sqlend = " WHERE contract = :id";
            $values[':id'] = $id;
        }

        foreach ($statuses as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $values[":{$key}"] = $value;

            // fecha
            $fields[] = "{$key}_date = :d{$key}";
            $values[":d{$key}"] = date('Y-m-d');

            // usuario
            $fields[] = "{$key}_user = :u{$key}";
            $values[":u{$key}"] = $user->id;
        }

        $sql .= " contract_status SET ";
        $sql .= implode(', ', $fields);
        $sql .= $sqlend;
        // die(\sqldbg($sql, $values));
        return (static::query($sql, $values)) ? true : false;
    }

    /**
     * Metodo para rellenar campo pdf
     * @param varchar(50) $id del proyecto
     * @param string $value  nombre del archivo
     * @return bool si ok
     */
    public function setPdf($name) {

        $sql = "UPDATE contract SET pdf = :pdf WHERE project = :id";
        $values = array(':id' => $this->project, ':pdf' => $name);

        return (static::query($sql, $values)) ? true : false;
    }



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
        } /*elseif ( !Check::nif($this->nif) ) {
            $errors['promoter']['nif'] = Text::get('validate-project-value-contract_nif');
        }*/ else {
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
                    $errors['entity']['reg_name'] = Text::get('mandatory-contract-reg_name_1');
                } else {
                     $okeys['entity']['reg_name'] = 'ok';
                }
                if (empty($this->reg_number)) {
                    $errors['entity']['reg_number'] = Text::get('mandatory-contract-reg_number_1');
                } else {
                     $okeys['entity']['reg_number'] = 'ok';
                }
            }

            // para representantes de entidad jurídica
            if ($this->type == 2) {
                if (empty($this->reg_name)) {
                    $errors['entity']['reg_name'] = Text::get('mandatory-contract-reg_name_1');
                } else {
                     $okeys['entity']['reg_name'] = 'ok';
                }
                if (empty($this->reg_date)) {
                    $errors['entity']['reg_date'] = Text::get('mandatory-contract-reg_date_2');
                } else {
                     $okeys['entity']['reg_date'] = 'ok';
                }
                if (empty($this->reg_number)) {
                    $errors['entity']['reg_number'] = Text::get('mandatory-contract-reg_number_2');
                } else {
                     $okeys['entity']['reg_number'] = 'ok';
                }
                if (empty($this->reg_id)) {
                    $errors['entity']['reg_id'] = Text::get('mandatory-contract-reg_id_2');
                } else {
                     $okeys['entity']['reg_id'] = 'ok';
                }
                if (empty($this->reg_idname)) {
                    $errors['entity']['reg_idname'] = Text::get('mandatory-contract-reg_idname_2');
                } else {
                     $okeys['entity']['reg_idname'] = 'ok';
                }
                if (empty($this->reg_idloc)) {
                    $errors['entity']['reg_idloc'] = Text::get('mandatory-contract-reg_idloc_2');
                } else {
                     $okeys['entity']['reg_idloc'] = 'ok';
                }
            }

        }
        /***************** FIN Revisión del paso ENTIDAD *****************/

        /***************** Revisión de campos del paso CUENTAS  *****************/
        if (!empty($this->paypal) && empty($this->paypal_owner)) {
            $errors['accounts']['paypal_owner'] = Text::get('mandatory-contract-paypal_owner');
        } else {
             $okeys['accounts']['paypal_owner'] = 'ok';
        }
        if (empty($this->bank)) {
            $errors['accounts']['bank'] = Text::get('mandatory-contract-bank');
        } else {
            $okeys['accounts']['bank'] = 'ok';
        }
        if (empty($this->bank_owner)) {
            $errors['accounts']['bank_owner'] = Text::get('mandatory-contract-bank_owner');
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

        foreach (License::getAll() as $l) {
            $licenses[$l->id] = $l;
        }

        $rews = 'El retorno colectivo que ofrece el proyecto consistirá en:';
        foreach ($projData->social_rewards as $social) {
            $rews .= "

- {$social->icon_name}: {$social->reward} Será publicado bajo licencia {$licenses[$social->license]->name} ({$licenses[$social->license]->url})";
        }

        return $rews;
    }

    /*
     * Estados de proceso de contrato
     */
    public static function procStatus () {
        return array(
            'noreg' => 'Sin registro de contrato',
            'onform' => 'Editando datos',
            'owner' => 'Formulario cerrado',
            'admin' => 'Datos en revision',
            'ready' => 'Listo para imprimir',
            'pdf' => 'Pdf descargado',
            'received' => 'Sobre recibido',
            'prepay' => 'Pago adelantado',
            'payed' => 'Pagos realizados',
            'closed' => 'Contrato cumplido'
            );
    }

    /*
     * Estados de proceso de contrato
     */
    public static function nextStatus ($actual) {

        // echo "actual: $actual<br />";

        $nexts = array();

        $estados = array( 'owner', 'admin', 'ready', 'pdf', 'received', 'payed', 'closed' );

        $ya = false;

        foreach ($estados as $key=>$value) {

            if ($ya) {
                $nexts[] = $value;
                // echo "añadido $value<br />";
            }

            if ($value == $actual) {
                $ya = true;
                // echo "desde ya $key == $actual <br />";
            }

        }

        return $nexts;

    }

}

