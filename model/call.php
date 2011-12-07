<?php

namespace Goteo\Model {

    use Goteo\Core\ACL,
        Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model\User,
        Goteo\Model\Image,
        Goteo\Model\Message;

    class Call extends \Goteo\Core\Model {

        public
            $id = null,
            $owner, // User who created it
            $node, // Node this call belongs to
            $status,
            $amount, // Presupuesto

            $user, // owner's user information

            // Register contract data
            $contract_name, // Nombre y apellidos del responsable del convocatoria
            $contract_nif, // Guardar sin espacios ni puntos ni guiones
            $contract_email, // cuenta paypal
            $phone, // guardar sin espacios ni puntos

            // Para marcar física o jurídica
            $contract_entity = false, // false = física (persona)  true = jurídica (entidad)

            // Para persona física
            $contract_birthdate,

            // Para entidad jurídica
            $entity_office, // cargo del responsable dentro de la entidad
            $entity_name,  // denomincion social de la entidad
            $entity_cif,  // CIF de la entidad

            // Campos de Domicilio: Igual para persona o entidad
            $address,
            $zipcode,
            $location, // owner's location
            $country,

            // Domicilio postal
            $secondary_address = false, // si es diferente al domicilio fiscal
            $post_address = null,
            $post_zipcode = null,
            $post_location = null,
            $post_country = null,


            // Edit call description
            $name,
            $subtitle,
            $lang = 'es',
            $logo,
            $image,
            $description,
            $whom, // quienes pueden participar
            $apply, // como publicar un convocatoria
            $legal, // terminos y condiciones
            $categories = array(),
            $icons = array(),
            $call_location, // call execution location

            $translate,  // si se puede traducir (bool)

            //Operative purpose properties
            $amount_used = 0,
            $amount_left = 0,

            $projects = array(); // convocatorias relacionados a la convocatoria



        /**
         * Inserta un convocatoria con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($name, $owner, &$errors = array()) {

            // El autor no tiene porque ser el que la edita
            
            // datos del usuario que van por defecto: name->contract_name,  location->location
            $userProfile = User::get($owner);
            // datos del userpersonal por defecto a los cammpos del paso 2
            $userPersonal = User::getPersonal($owner);

            $id = self::idealiza($name);

            $values = array(
                ':id'   => $id,
                ':name' => $name,
                ':lang' => 'es',
                ':status'   => 1,
                ':owner' => $owner,
                ':amount' => 0,
                ':contract_name' => ($userPersonal->contract_name) ?
                                    $userPersonal->contract_name :
                                    $userProfile->name,
                ':contract_nif' => $userPersonal->contract_nif,
                ':phone' => $userPersonal->phone,
                ':address' => $userPersonal->address,
                ':zipcode' => $userPersonal->zipcode,
                ':location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                ':country' => ($userPersonal->country) ?
                                $userPersonal->country :
                                Check::country(),
                ':call_location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                );

            $campos = array();
            foreach (\array_keys($values) as $campo) {
                $campos[] = \str_replace(':', '', $campo);
            }

            $sql = "REPLACE INTO call (" . implode(',', $campos) . ")
                 VALUES (" . implode(',', \array_keys($values)) . ")";
            try {
				self::query($sql, $values);

                foreach ($campos as $campo) {
                    $this->$campo = $values[":$campo"];
                }

                return $this->id;
            } catch (\PDOException $e) {
                $errors[] = "ERROR al crear una nueva convocatoria<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>";
                \trace($this);
                die($errors[0]);
                return false;
            }
        }

        /*
         *  Cargamos los datos del convocatoria
         */
        public static function get($id, $lang = null) {

            try {
				// metemos los datos del convocatoria en la instancia
				$query = self::query("SELECT * FROM call WHERE id = ?", array($id));
				$call = $query->fetchObject(__CLASS__);

                if (!$call instanceof \Goteo\Model\Call) {
                    throw new \Goteo\Core\Error('404', Text::html('fatal-error-call'));
                }

                // si recibimos lang y no es el idioma original del convocatoria, ponemos la traducción y mantenemos para el resto de contenido
                // ESTO LUEGO  //@TODO tema Lang
                /*
                if ($lang == $call->lang) {
                    $lang = null;
                } elseif (!empty($lang)) {
                    $sql = "
                        SELECT
                            IFNULL(call_lang.description, call.description) as description,
                            IFNULL(call_lang.motivation, call.motivation) as motivation,
                            IFNULL(call_lang.video, call.video) as video,
                            IFNULL(call_lang.about, call.about) as about,
                            IFNULL(call_lang.goal, call.goal) as goal,
                            IFNULL(call_lang.related, call.related) as related,
                            IFNULL(call_lang.keywords, call.keywords) as keywords,
                            IFNULL(call_lang.media, call.media) as media,
                            IFNULL(call_lang.subtitle, call.subtitle) as subtitle
                        FROM call
                        LEFT JOIN call_lang
                            ON  call_lang.id = call.id
                            AND call_lang.lang = :lang
                        WHERE call.id = :id
                        ";
                    $query = self::query($sql, array(':id'=>$id, ':lang'=>$lang));
                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field=>$value) {
                        $call->$field = $value;
                    }
                }
                 * 
                 */

                // owner
                $call->user = User::get($call->owner);

                // logo
                $call->logo = Image::get($call->logo);

                // imagen
                $call->image = Image::get($call->image);

				// categorias
                $call->categories = Call\Category::get($id);

				// iconos de retorno
                $call->icons = Call\Icon::get($id);

				// proyectos
				$call->projects = Call\Project::get($id);

//                $call->usedCalc();

                // para convocatorias en campaña o posterior
                // los proyectos han conseguido pasta, son exitosos, estan en campaña o no han conseguido y estan caducados pero no se calculan ni dias ni ronda

				return $call;

			} catch(\PDOException $e) {
				throw \Goteo\Core\Exception($e->getMessage());
			} catch(\Goteo\Core\Error $e) {
                throw new \Goteo\Core\Error('404', Text::html('fatal-error-call'));
			}
		}

        /*
         *  Cargamos los datos mínimos de un convocatoria
         *  para pintar en otras páginas
         */
        public static function getMini($id) {

            try {
				// metemos los datos del convocatoria en la instancia
				$query = self::query("SELECT id, name, owner, lang FROM call WHERE id = ?", array($id));
				$call = $query->fetchObject(); // stdClass para qno grabar accidentalmente y machacar todo

                // owner
                $call->user = User::getMini($call->owner);

				return $call;

			} catch(\PDOException $e) {
				throw \Goteo\Core\Exception($e->getMessage());
			}
		}

        /*
         * Listado simple de todos los convocatorias
         */
        public static function getAll() {

            $list = array();

            $query = static::query("
                SELECT
                    call.id as id,
                    call.name as name
                FROM    call
                ORDER BY call.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         *  Para validar los campos del convocatoria que son NOT NULL en la tabla
         */
        public function validate(&$errors = array()) {

            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'El convocatoria no tiene id';
                //Text::get('validate-call-noid');

            if (empty($this->name))
                $errors[] = 'El convocatoria no tiene nombre';
                //Text::get('validate-call-noname');

            if (empty($this->lang))
                $this->lang = 'es';

            if (empty($this->status))
                $this->status = 1;
            
            if (empty($this->owner))
                $errors[] = 'El convocatoria no tiene usuario dueño';
                //Text::get('validate-call-noowner');
            
            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

        /**
         * actualiza en la tabla los datos del convocatoria
         * @param array $call->errors para guardar los errores de datos del formulario, los errores de proceso se guardan en $call->errors['process']
         */
        public function save (&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                // fail para pasar por todo antes de devolver false
                $fail = false;

                // los nif sin guiones, espacios ni puntos
                $this->contract_nif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->contract_nif);
                $this->entity_cif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->entity_cif);

                // Logo
                if (is_array($this->logo) && !empty($this->logo['name'])) {
                    $logo = new Image($this->logo);
                    if ($logo->save($errors)) {
                        $this->logo = $logo->id;
                    } else {
                        \Goteo\Library\Message::Error(Text::get('call-logo-upload-fail') . implode(', ', $errors));
                    }
                }

                // Imagen de fondo
                if (is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    if ($image->save($errors)) {
                        $this->image = $image->id;
                    } else {
                        \Goteo\Library\Message::Error(Text::get('call-image-upload-fail') . implode(', ', $errors));
                    }
                }

                $fields = array(
                    'contract_name',
                    'contract_nif',
                    'contract_email',
                    'contract_entity',
                    'contract_birthdate',
                    'entity_office',
                    'entity_name',
                    'entity_cif',
                    'phone',
                    'address',
                    'zipcode',
                    'location',
                    'country',
                    'secondary_address',
                    'post_address',
                    'post_zipcode',
                    'post_location',
                    'post_country',
                    'name',
                    'subtitle',
                    'logo',
                    'image',
                    'description',
                    'whom',
                    'apply',
                    'legal',
                    'call_location'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$field;
                }

                // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
//				$set .= ", updated = :updated";
//				$values[':updated'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE call SET " . $set . " WHERE id = :id";
				if (!self::query($sql, $values)) {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    $fail = true;
                }

//                echo "$sql<br />";
                // y aquí todas las tablas relacionadas
                // cada una con sus save, sus new y sus remove
                // quitar las que tiene y no vienen
                // añadir las que vienen y no tiene

                //categorias
                $tiene = Call\Category::get($this->id);
                $viene = $this->categories;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    $category = new Call\Category(
                        array(
                            'id'=>$item,
                            'call'=>$this->id)
                    );
                    if (!$category->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->categories = Call\Category::get($this->id);

                //iconos
                $tiene = Call\Icon::get($this->id);
                $viene = $this->icons;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    $icon = new Call\Icon(
                        array(
                            'id'=>$item,
                            'call'=>$this->id)
                    );
                    if (!$icon->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->icons = Call\Icon::get($this->id);


                //listo
                return !$fail;
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el convocatoria.' . $e->getMessage();
                //Text::get('save-call-fail');
                return false;
			}

        }


        public function saveLang (&$errors = array()) {

  			try {
                $fields = array(
                    'id'=>'id',
                    'lang'=>'lang_lang',
                    'subtitle'=>'subtitle_lang',
                    'description'=>'description_lang',
                    'whom'=>'whom_lang',
                    'apply'=>'apply_lang',
                    'legal'=>'legal_lang'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field=>$ffield) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$ffield;
                }

				$sql = "REPLACE INTO call_lang SET " . $set;
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar la traduccion de la convocatoria.' . $e->getMessage();
                //Text::get('save-call-fail');
                return false;
			}

        }

        /*
         * Listo para entrada de proyectos
         */
        public function ready(&$errors = array()) {
			try {
                $sql = "UPDATE call SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
                
                return true;
                
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al finalizar la edicion. ' . $e->getMessage();
                //Text::get('send-call-review-fail');
                return false;
            }
        }

        /*
         * Devuelto al estado de edición o selección propia de proyectos
         */
        public function enable(&$errors = array()) {
			try {
				$sql = "UPDATE call SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>1, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para edición. ' . $e->getMessage();
                //Text::get('send-call-reedit-fail');
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         *  Ya aparecen los proyectos seleccionados para aportarles
         */
        public function publish(&$errors = array()) {
			try {
				$sql = "UPDATE call SET status = :status, published = :published WHERE id = :id";
				self::query($sql, array(':status'=>3, ':published'=>date('Y-m-d'), ':id'=>$this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el convocatoria. ' . $e->getMessage();
                //Text::get('send-call-publish-fail');
                return false;
            }
        }

        /*
         * Cambio a estado caducado,
         *  parada de emergencia antes de terminar el dinero
         */
        public function fail(&$errors = array()) {
			try {
				$sql = "UPDATE call SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>6, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el convocatoria. ' . $e->getMessage();
                //Text::get('send-projecct-close-fail');
                return false;
            }
        }

        /*
         * Cambio a estado Financiado,
         *  no queda más dinero.
         */
        public function succeed(&$errors = array()) {
			try {
				$sql = "UPDATE call SET status = :status, success = :success WHERE id = :id";
				self::query($sql, array(':status'=>4, ':success'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar por financiado el convocatoria. ' . $e->getMessage();
                //Text::get('send-call-success-fail');
                return false;
            }
        }

        /*
         * Si no se pueden borrar todos los registros, estado cero para que lo borre el cron
         */
        public function delete(&$errors = array()) {

            if ($this->status != 1) {
                return false;
            }

            self::query("START TRANSACTION");
            try {
                //borrar todos los registros
                self::query("DELETE FROM call_category WHERE call = ?", array($this->id));
                self::query("DELETE FROM call_icon WHERE call = ?", array($this->id));
                self::query("DELETE FROM call_project WHERE call = ?", array($this->id));
                self::query("DELETE FROM call WHERE id = ?", array($this->id));
                // y los permisos
                self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));
                // si todo va bien, commit y cambio el id de la instancia
                self::query("COMMIT");
                return true;
            } catch (\PDOException $e) {
                self::query("ROLLBACK");
				$sql = "UPDATE call SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>0, ':id'=>$this->id));
                return false;
            }
        }

        /*
         * No hay rebase, obligamos a poner el nombre y el dueño antes de crearla
         */
        public function rebase() {
            return false;
        }

        /*
         *  Para verificar id única
         */
        public static function checkId($id, $num = 1) {
            try
            {
                $query = self::query("SELECT id FROM call WHERE id = :id", array(':id'=>$id));
                $exist = $query->fetchObject();
                // si  ya existe, cambiar las últimas letras por un número
                if (!empty($exist->id)) {
                    $sufix = (string) $num;
                    if ((strlen($id)+strlen($sufix)) > 49)
                        $id = substr($id, 0, (strlen($id) - strlen($sufix))) . $sufix;
                    else
                        $id = $id . $sufix;
                    $num++;
                    $id = self::checkId($id, $num);
                }
                return $id;
            }
            catch (\PDOException $e) {
                throw new Goteo\Core\Exception('Fallo al verificar id única para el convocatoria. ' . $e->getMessage());
            }
        }

        /*
         *  Para actualizar el presupuesto usado y restante
         *   sumamos de los aportes en estado ok que sean para esta campaña
         */
        public function minmax() {
            $this->mincost = 0;
            $this->maxcost = 0;
        }

        /*
         * Lista de convocatorias de un usuario
         */
        public static function ofmine($owner, $published = false)
        {
            $calls = array();

            $sql = "SELECT * FROM call WHERE owner = ?";
            if ($published) {
                $sql .= " AND status > 2";
            } else {
                $sql .= " AND status > 0";
            }
            $sql .= " ORDER BY created DESC";
            $query = self::query($sql, array($owner));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $calls[] = self::get($proj->id);
            }
            
            return $calls;
        }

        /*
         * Lista de convocatorias publicados
         *
        public static function published($type = 'all', $limit = null)
        {
            // segun el tipo (ver controller/discover.php)
            switch ($type) {
                case 'popular':
                    // de los que estan en campaña,
                    // los que tienen más usuarios (unicos) cofinanciadores y mensajeros
                    $sql = "SELECT COUNT(DISTINCT(user.id)) as people, call.id as id
                            FROM call
                            LEFT JOIN invest
                                ON invest.call = call.id
                                AND invest.status <> 2
                            LEFT JOIN message
                                ON message.call = call.id
                            LEFT JOIN user 
                                ON user.id = invest.user OR user.id = message.user
                            WHERE call.status= 3
                            AND (call.id = invest.call
                                OR call.id = message.call)
                            GROUP BY call.id
                            ORDER BY people DESC";
                    break;
                case 'outdate':
                    // los que les quedan 15 dias o menos
                    $sql = "SELECT  id
                            FROM    call
                            WHERE   days <= 15
                            AND     days > 0
                            AND     status = 3
                            ORDER BY days ASC";
                    break;
                case 'recent':
                    // los que llevan menos tiempo desde el published, hasta 15 dias
                    $sql = "SELECT 
                                call.id as id,
                                DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%e') as day
                            FROM call
                            WHERE call.status = 3
                            HAVING day <= 15 AND day IS NOT NULL
                            ORDER BY published DESC";
                    break;
                case 'success':
                    // los que estan 'financiado' o 'retorno cumplido'
                    $sql = "SELECT id FROM call WHERE status = 4 OR status = 5 ORDER BY name ASC";
                    break;
                case 'available':
                    // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                    $sql = "SELECT id FROM call WHERE status > 2 AND status < 6 ORDER BY name ASC";
                    break;
                default: 
                    // todos los que estan 'en campaña'
                    $sql = "SELECT id FROM call WHERE status = 3 ORDER BY name ASC";
            }

            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

            $calls = array();
            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $calls[] = self::get($proj['id']);
            }
            return $calls;
        }
         * 
         */

        /*
         * Lista de convocatorias en campaña (para ser revisados por el cron)
         *
        public static function active()
        {
            $calls = array();
            $query = self::query("SELECT call.id
                                  FROM  call
                                  WHERE call.status = 3 OR call.status = 4
                                  GROUP BY call.id
                                  ORDER BY name ASC");
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $calls[] = self::get($proj->id);
            }
            return $calls;
        }
         *
         */

        /**
         * Saca una lista completa de convocatorias
         *  para gestión
         *
         * @param array filters
         * @return array of call instances
         */
        public static function getList($filters = array()) {
            $calls = array();

            // los filtros
            $sqlFilter = "";
            if (!empty($filters['status'])) {
                $sqlFilter .= " AND status = :status";
                $values[':status'] = $filters['status'];
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND name LIKE :name";
                $values[':name'] = "%{$filters['name']}%";
            }
            if (!empty($filters['category'])) {
                $sqlFilter .= " AND id IN (
                    SELECT call
                    FROM call_category
                    WHERE category = :category
                    )";
                $values[':category'] = $filters['category'];
            }

            if (!empty($filters['icon'])) {
                $sqlFilter .= " AND id IN (
                    SELECT call
                    FROM call_icon
                    WHERE icon = :icon
                    )";
                $values[':icon'] = $filters['icon'];
            }

            //el Order
            if (!empty($filters['order'])) {
                switch ($filters['order']) {
                    case 'updated':
                        $sqlOrder .= " ORDER BY updated DESC";
                    break;
                    case 'name':
                        $sqlOrder .= " ORDER BY name ASC";
                    break;
                    default:
                        $sqlOrder .= " ORDER BY {$filters['order']}";
                    break;
                }
            }

            // la select
            $sql = "SELECT 
                        id
                    FROM call
                    WHERE status > 0
                        $sqlFilter
                        $sqlOrder
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $calls[] = self::get($proj['id']);
            }
            return $calls;
        }

        /*
         * Estados de publicación de un convocatoria
         */
        public static function status () {
            return array(
//                0=>Text::get('form-call_status-cancelled'),
                1=>Text::get('form-call_status-edit'),
                2=>Text::get('form-call_status-review'),
                3=>Text::get('form-call_status-campaing'),
                4=>Text::get('form-call_status-success'),
                5=>Text::get('form-call_status-fulfilled'),
                6=>Text::get('form-call_status-expired'));
        }

    }

}