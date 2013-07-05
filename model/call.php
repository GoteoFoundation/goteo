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
        $maxdrop, // Limite al capital riego que puede provocar cada aporte
        $maxproj, // Limite al capital riego que puede conseguir un proyecto
        $modemaxp, // Modalidad del máximo por proyecto (importe o porcentaje sobre mínimo)
        $resources, // Recursos de capital riego
        $days, // Numero de dias para aplicación de proyectos
        $until = array('day' => '', 'month' => '', 'year' => ''), // para visualizar fecha limite en estado aplicación

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
        $entity_name, // denomincion social de la entidad
        $entity_cif, // CIF de la entidad
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
        $logo,  // imagen de cabecera
        $image, // imagen para el widget
        $backimage, //imagen de fondo
        $description,
        $whom, // quienes pueden participar
        $apply, // como publicar un convocatoria
        $legal, // terminos y condiciones
        $dossier, // url del dosier informativo
        $tweet, // texto con los hashtags en twitter
        $fbappid = null, // id de la app de facebook
        $categories = array(),
        $icons = array(),
        $call_location, // call execution location
        $scope, // ambito

        $errors = array(), // para los fallos en los datos
        $okeys = array(), // para los campos que estan ok

        $translate, // si se puede traducir (bool)

        $projects = array(), // proyectos seleccionados en la convocatoria
        $sponsors = array(), // patrocinadores de la convocatoria
        $banners  = array(), // banners de la convocatoria

        $expired = false; // si ha finalizado el tiempo de inscripcion

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */

        public function __get($name) {
            switch ($name) {
                case "rest":
                    return $this->getRest();
                    break;
                case "used":
                    return $this->getRest(true);
                    break;

                case "applied":
                    // número de proyectos presentados a la campaña
                    $applied = array(
                        'unia-capital-riego' => 44,
                        'crowdsasuna' => 26
                    );
                    return (isset($applied[$this->id])) ? $applied[$this->id] : $this->getApplied();
                    break;

                default:
                    return $this->$name;
            }
        }

        /**
         * Inserta un convocatoria con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create($name, $owner, &$errors = array()) {

            // El autor no tiene porque ser el que la edita
            // datos del usuario que van por defecto: name->contract_name,  location->location
            $userProfile = User::get($owner);
            // datos del userpersonal por defecto a los cammpos del paso 2
//            $userPersonal = User::getPersonal($owner); // no hay más paso 2 en este formulario

            // debe verificar que puede conseguir un id único a partir del nombre
            $id = self::checkId(self::idealiza($name));
            if ($id == false) {
                $errors[] = 'No se ha podido generar Id';
                return false;
            }

            $values = array(
                ':id' => $id,
                ':name' => $name,
                ':lang' => 'es',
                ':status' => 1,
                ':owner' => $owner,
                ':amount' => 0,
                /*
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
                 */
                ':call_location' => $userProfile->location,
                ':tweet' => $name,
            );

            $campos = array();
            foreach (\array_keys($values) as $campo) {
                $campos[] = \str_replace(':', '', $campo);
            }

            $sql = "REPLACE INTO `call` (" . implode(',', $campos) . ")
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
                $query = self::query("SELECT * FROM `call` WHERE id = :id", array(':id' => $id));
                $call = $query->fetchObject(__CLASS__);

                if (!$call instanceof \Goteo\Model\Call) {
                    throw new \Goteo\Core\Error('404', Text::html('fatal-error-call'));
                }

                // si recibimos lang y no es el idioma original del convocatoria, ponemos la traducción y mantenemos para el resto de contenido
                if ($lang == $call->lang) {
                    $lang = null;
                } elseif (!empty($lang)) {
                    $sql = "
                        SELECT
                            IFNULL(call_lang.name, call.name) as name,
                            IFNULL(call_lang.subtitle, call.subtitle) as subtitle,
                            IFNULL(call_lang.description, call.description) as description,
                            IFNULL(call_lang.whom, call.whom) as whom,
                            IFNULL(call_lang.apply, call.apply) as apply,
                            IFNULL(call_lang.legal, call.legal) as legal,
                            IFNULL(call_lang.dossier, call.dossier) as dossier,
                            IFNULL(call_lang.resources, call.resources) as resources,
                            IFNULL(call_lang.tweet, call.tweet) as tweet
                        FROM `call`
                        LEFT JOIN call_lang
                            ON  call_lang.id = call.id
                            AND call_lang.lang = :lang
                        WHERE call.id = :id
                        ";
                    $query = self::query($sql, array(':id' => $id, ':lang' => $lang));
                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field => $value) {
                        $call->$field = $value;
                    }
                }

                // owner
                $call->user = User::get($call->owner);

                // No vamos a hacer aqui objetos para las imagenes, los hacemos en el controlador
                // categorias
                $call->categories = Call\Category::get($id);

                // iconos de retorno
                $call->icons = Call\Icon::get($id);

                // proyectos
                $call->projects = Call\Project::get($id);

                // entrada blog
                $call->post = Call\Post::get($id);

                // cuantos en campaña (status 3) y cuantos exitosos
                $call->runing_projects = 0;
                $call->success_projects = 0;

                foreach ($call->projects as $proj) {
                    if (\Goteo\Model\Project::isSuccessful($proj->id)) {
                        $call->success_projects++;
                    } 
                    if ($proj->status == 3) {
                        $call->runing_projects++;
                    }
                }

                // para convocatorias en campaña o posterior
                // los proyectos han conseguido pasta, son exitosos, estan en campaña o no han conseguido y estan caducados pero no se calculan ni dias ni ronda

                if ($call->status == 3) {
                    // a ver si ya ha expirado
                    $open = strtotime($call->opened);
                    $until = mktime(0, 0, 0, date('m', $open), date('d', $open) + $call->days, date('Y', $open));
                    $hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                    if ($hoy > $until) {
                        $call->expired = true;
                    }

                    // rellenamos el array de visualizacion de fecha limite
                    $call->until['day'] = date('d', $until);
                    $call->until['month'] = strftime('%b', $until);
                    ;
                    $call->until['year'] = date('Y', $until);
                }

                $call->sponsors = Call\Sponsor::getList($id);
                $call->banners  = Call\Banner::getList($id, $lang);

                return $call;
            } catch (\PDOException $e) {
                throw \Goteo\Core\Exception($e->getMessage());
            } catch (\Goteo\Core\Error $e) {
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
                $query = self::query("SELECT id, name, owner, lang FROM `call` WHERE id = ?", array($id));
                $call = $query->fetchObject(); // stdClass para qno grabar accidentalmente y machacar todo
                // owner
                $call->user = User::getMini($call->owner);

                return $call;
            } catch (\PDOException $e) {
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
                FROM `call`
                ORDER BY call.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         *  Devuelve simplemente el número de proyectos asignados a esta convocatoria
         *  No cuentan los draft
         */
        public function getApplied() {
                $sql = "SELECT
                            COUNT(project.id) as cuantos
                        FROM project
                        INNER JOIN call_project
                            ON  call_project.project = project.id
                            AND call_project.call = :call
                        WHERE (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{5,40}') )
                        ";
                
                $query = static::query($sql, array(':call'=>$this->id));
                $cuantos = $query->fetchColumn();
                
                return $cuantos;
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
        public function save(&$errors = array()) {
            if (!$this->validate($errors)) {
                return false;
            }

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

                // Imagen de fondo splash
                if (is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    if ($image->save($errors)) {
                        $this->image = $image->id;
                    } else {
                        \Goteo\Library\Message::Error(Text::get('call-image-upload-fail') . implode(', ', $errors));
                    }
                }

                // Imagen de fondo resto de páginas
                if (is_array($this->backimage) && !empty($this->backimage['name'])) {
                    $backimage = new Image($this->backimage);
                    if ($backimage->save($errors)) {
                        $this->backimage = $backimage->id;
                    } else {
                        \Goteo\Library\Message::Error(Text::get('call-backimage-upload-fail') . implode(', ', $errors));
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
                    'backimage',
                    'description',
                    'whom',
                    'apply',
                    'legal',
                    'dossier',
                    'tweet',
                    'fbappid',
                    'call_location',
                    'resources',
                    'scope',
                    'amount',
                    'maxdrop',
                    'maxproj',
                    'modemaxp',
                    'days'
                );

                $set = '';
                $values = array();

                foreach ($fields as $field) {
                    if ($set != '')
                        $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$field;
                }

                // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
//				$set .= ", updated = :updated";
//				$values[':updated'] = date('Y-m-d');
                $values[':id'] = $this->id;

                $sql = "UPDATE `call` SET " . $set . " WHERE id = :id";
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
                foreach ($quita as $key => $item) {
                    $category = new Call\Category(
                                    array(
                                        'id' => $item,
                                        'call' => $this->id)
                    );
                    if (!$category->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key => $item) {
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
                foreach ($quita as $key => $item) {
                    $icon = new Call\Icon(
                                    array(
                                        'id' => $item,
                                        'call' => $this->id)
                    );
                    if (!$icon->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key => $item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->icons = Call\Icon::get($this->id);

                //banners
                $tiene = Call\Banner::getList($this->id);
                $viene = $this->banners;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!Call\Banner::delete($item->id)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                        $guarda = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
                    $this->banners = Call\Banner::getList($this->id);


                //sponsors
                $tiene = Call\Sponsor::getList($this->id);
                $viene = $this->sponsors;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!Call\Sponsor::delete($item->id)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                        $guarda = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
                    $this->sponsors = Call\Sponsor::getList($this->id);


                //listo
                return !$fail;
            } catch (\PDOException $e) {
                $errors[] = 'Error sql al grabar el convocatoria.' . $e->getMessage();
                return false;
            }
        }

        public function saveLang(&$errors = array()) {

            try {
                $fields = array(
                    'id' => 'id',
                    'lang' => 'lang_lang',
                    'name' => 'name_lang',
                    'subtitle' => 'subtitle_lang',
                    'description' => 'description_lang',
                    'whom' => 'whom_lang',
                    'apply' => 'apply_lang',
                    'legal' => 'legal_lang',
                    'resources' => 'resources_lang',
                    'dossier' => 'dossier_lang',
                    'tweet' => 'tweet_lang'
                );

                $set = '';
                $values = array();

                foreach ($fields as $field => $ffield) {
                    if ($set != '')
                        $set .= ', ';
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
            } catch (\PDOException $e) {
                $errors[] = 'Error sql al grabar la traduccion de la convocatoria.' . $e->getMessage();
                return false;
            }
        }

        /*
         * Listo para revision
         */
        public function ready(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status' => 2, ':updated' => date('Y-m-d'), ':id' => $this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al finalizar la edicion. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Listo para postulación
         */
        public function open(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status, opened = :opened WHERE id = :id";
                self::query($sql, array(':status' => 3, ':opened' => date('Y-m-d'), ':id' => $this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al abrir la postulacion. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelto al estado de edición o selección propia de proyectos
         */
        public function enable(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status WHERE id = :id";
                self::query($sql, array(':status' => 1, ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para edición. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         *  Ya aparecen los proyectos seleccionados para aportarles
         */
        public function publish(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status, published = :published WHERE id = :id";
                self::query($sql, array(':status' => 4, ':published' => date('Y-m-d'), ':id' => $this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar la convocatoria. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado caducado,
         *  parada de emergencia antes de terminar el dinero
         */
        public function fail(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status, closed = :closed WHERE id = :id";
                self::query($sql, array(':status' => 6, ':closed' => date('Y-m-d'), ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar la convocatoria. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Financiado,
         *  no queda más dinero.
         */
        public function succeed(&$errors = array()) {
            try {
                $sql = "UPDATE `call` SET status = :status, success = :success WHERE id = :id";
                self::query($sql, array(':status' => 5, ':success' => date('Y-m-d'), ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar por financiado la convocatoria. ' . $e->getMessage();
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
                self::query("DELETE FROM call_category WHERE `call` = ?", array($this->id));
                self::query("DELETE FROM call_icon WHERE `call` = ?", array($this->id));
                self::query("DELETE FROM call_project WHERE `call` = ?", array($this->id));
                self::query("DELETE FROM call_banner WHERE `call` = ?", array($this->id));
                self::query("DELETE FROM call_sponsor WHERE `call` = ?", array($this->id));
                self::query("DELETE FROM `call` WHERE id = ?", array($this->id));
                // y los permisos
                self::query("DELETE FROM acl WHERE url LIKE :call AND url LIKE :id", array(':call' => '%/call/%', ':id' => '%' . $this->id . '%'));
                // si todo va bien, commit y cambio el id de la instancia
                self::query("COMMIT");
                return true;
            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $sql = "UPDATE `call` SET status = :status WHERE id = :id";
                self::query($sql, array(':status' => 0, ':id' => $this->id));
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
            try {
                $query = self::query("SELECT id FROM `call` WHERE id = :id", array(':id' => $id));
                $exist = $query->fetchObject();
                // si  ya existe, cambiar las últimas letras por un número
                if (!empty($exist->id)) {
                    $sufix = (string) $num;
                    if ((strlen($id) + strlen($sufix)) > 49)
                        $id = substr($id, 0, (strlen($id) - strlen($sufix))) . $sufix;
                    else
                        $id = $id . $sufix;
                    $num++;
                    $id = self::checkId($id, $num);
                }
                return $id;
            } catch (\PDOException $e) {
                throw new Goteo\Core\Exception('Fallo al verificar id única para la convocatoria. ' . $e->getMessage());
            }
        }

        /*
         * Lista de convocatorias de un usuario
         */
        public static function ofmine($owner, $published = false) {
            $calls = array();

            $sql = "SELECT * FROM `call` WHERE owner = ?";
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
         * Lista de convocatorias en campaña (para la portada)
         */
        public static function getActive($status = null, $all = false) {
            $calls = array();
            $values = array();

            if (in_array($status, array(3, 4, 5))) {
                $sqlFilter .= " WHERE call.status = $status"; // solo cierto estado
            } elseif ($all) {
                $sqlFilter .= " WHERE call.status IN ('3', '4', '5')"; // desde aplicacion hasta exitosa
            } else {
                $sqlFilter .= " WHERE call.status IN ('3', '4')"; // solo aplicacion y campaña
            }

            if (\NODE_ID != \GOTEO_NODE) {
                $sqlFilter .= " AND call.id IN (SELECT `call` FROM campaign WHERE node = :node and active = 1) ";
                $values[':node'] = \NODE_ID;
            }

            $sql = "SELECT call.id
                    FROM  `call`
                    $sqlFilter
                    ORDER BY name ASC";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $call) {
                $calls[] = self::get($call->id, \LANG);
            }
            return $calls;
        }

        /*
         * Lista de convocatorias a las que se le puede asignar otro proyecto
         */
        public static function getAvailable($wProj = false) {
            $calls = array();

            // en aplicación, en campaña o finalizadas
            if ($wProj) {
                $sqlFilter .= " WHERE call.status > 2";
            } else {
                $sqlFilter .= " WHERE call.status IN ('1', '2', '3', '4')"; // desde edicion hasta en campaña
            }

            $sql = "SELECT call.id, call.name
                    FROM  `call`
                    $sqlFilter
                    ORDER BY name ASC";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $call) {
                $calls[$call->id] = $call->name;
            }
            return $calls;
        }

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
            if (!empty($filters['caller'])) {
                $sqlFilter .= " AND owner = :caller";
                $values[':caller'] = $filters['caller'];
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

            if (!empty($filters['admin'])) {
                $sqlFilter .= " AND id IN (SELECT `call` FROM user_call WHERE user = '{$filters['admin']}')";
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
                    FROM `call`
                    WHERE status > 0
                        $sqlFilter
                        $sqlOrder
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $call) {
                $calls[] = self::get($call['id']);
            }
            return $calls;
        }

        /**
         * Saca una lista de convocatorias disponibles para traducir
         *
         * @param array filters
         * @param string node id
         * @return array of project instances
         */
        public static function getTranslates($filters = array()) {
            $projects = array();

            $values = array();

            $sqlFilter = "";
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['translator'])) {
                $sqlFilter .= " AND id IN (
                    SELECT item
                    FROM user_translate
                    WHERE user = :translator
                    AND type = 'call'
                    )";
                $values[':translator'] = $filters['translator'];
            }

//                    AND node = :node
            $sql = "SELECT
                        id
                    FROM `call`
                    WHERE translate = 1
                        $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::getMini($proj['id']);
            }
            return $projects;
        }

        /*
         * comprueba errores de datos y actualiza la puntuación
         */
        public function check() {
            //primero resetea los errores y los okeys
            $this->errors = self::blankErrors();
            $this->okeys = self::blankErrors();

            $errors = &$this->errors;
            $okeys = &$this->okeys;

            /* *************** Revisión de campos del paso 1, PERFIL **************** */
            // obligatorios: nombre, email, ciudad
            if (empty($this->user->name)) {
                $errors['userProfile']['name'] = Text::get('validate-user-field-name');
            } else {
                $okeys['userProfile']['name'] = 'ok';
            }

            if (empty($this->user->location)) {
                $errors['userProfile']['location'] = Text::get('validate-user-field-location');
            } else {
                $okeys['userProfile']['location'] = 'ok';
            }

            if (!empty($this->user->avatar) && $this->user->avatar->id != 1) {
                $okeys['userProfile']['avatar'] = 'ok';
            }

            if (!empty($this->user->about)) {
                $okeys['userProfile']['about'] = 'ok';

                // además error si tiene más de 2000
                if (\strlen($this->user->about) > 2000) {
                    $errors['userProfile']['about'] = Text::get('validate-user-field-about');
                    unset($okeys['userProfile']['about']);
                }
            }

            if (!empty($this->user->interests)) {
                $okeys['userProfile']['interests'] = 'ok';
            }

            if (!empty($this->user->keywords)) {
                $okeys['userProfile']['keywords'] = 'ok';
            }

            if (!empty($this->user->contribution)) {
                $okeys['userProfile']['contribution'] = 'ok';
            }

            if (empty($this->user->webs)) {
                $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-web');
            } else {
                $okeys['userProfile']['webs'] = 'ok';

                $anyerror = false;
                foreach ($this->user->webs as $web) {
                    if (trim(str_replace('http://', '', $web->url)) == '') {
                        $anyerror = !$anyerror ? : true;
                        $errors['userProfile']['web-' . $web->id . '-url'] = Text::get('validate-user-field-web');
                    } else {
                        $okeys['userProfile']['web-' . $web->id . '-url'] = 'ok';
                    }
                }

                if ($anyerror) {
                    unset($okeys['userProfile']['webs']);
                    $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-any_error');
                }
            }

            if (!empty($this->user->facebook)) {
                $okeys['userProfile']['facebook'] = 'ok';
            }

            if (!empty($this->user->twitter)) {
                $okeys['userProfile']['twitter'] = 'ok';
            }

            if (!empty($this->user->linkedin)) {
                $okeys['userProfile']['linkedin'] = 'ok';
            }

            /* *************** FIN Revisión del paso 1, PERFIL **************** */

            /* *************** Revisión de campos del paso 2,DATOS PERSONALES **************** */
            // obligatorios: todos
            if (empty($this->contract_name)) {
                $errors['userPersonal']['contract_name'] = Text::get('mandatory-project-field-contract_name');
            } else {
                $okeys['userPersonal']['contract_name'] = 'ok';
            }

            if (empty($this->contract_nif)) {
                $errors['userPersonal']['contract_nif'] = Text::get('mandatory-project-field-contract_nif');
            } elseif (!Check::nif($this->contract_nif)) {
                $errors['userPersonal']['contract_nif'] = Text::get('validate-project-value-contract_nif');
            } else {
                $okeys['userPersonal']['contract_nif'] = 'ok';
            }

            if (empty($this->contract_email)) {
                $errors['userPersonal']['contract_email'] = Text::get('mandatory-project-field-contract_email');
            } elseif (!Check::mail($this->contract_email)) {
                $errors['userPersonal']['contract_email'] = Text::get('validate-project-value-contract_email');
            } else {
                $okeys['userPersonal']['contract_email'] = 'ok';
            }

            // Segun persona física o jurídica
            if ($this->contract_entity) {  // JURIDICA
                if (empty($this->entity_office)) {
                    $errors['userPersonal']['entity_office'] = Text::get('mandatory-project-field-entity_office');
                } else {
                    $okeys['userPersonal']['entity_office'] = 'ok';
                }

                if (empty($this->entity_name)) {
                    $errors['userPersonal']['entity_name'] = Text::get('mandatory-project-field-entity_name');
                } else {
                    $okeys['userPersonal']['entity_name'] = 'ok';
                }

                if (empty($this->entity_cif)) {
                    $errors['userPersonal']['entity_cif'] = Text::get('mandatory-project-field-entity_cif');
                } elseif (!Check::nif($this->entity_cif)) {
                    $errors['userPersonal']['entity_cif'] = Text::get('validate-project-value-entity_cif');
                } else {
                    $okeys['userPersonal']['entity_cif'] = 'ok';
                }
            } else { // FISICA
                if (empty($this->contract_birthdate)) {
                    $errors['userPersonal']['contract_birthdate'] = Text::get('mandatory-project-field-contract_birthdate');
                } else {
                    $okeys['userPersonal']['contract_birthdate'] = 'ok';
                }
            }


            if (empty($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('mandatory-project-field-phone');
            } elseif (!Check::phone($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('validate-project-value-phone');
            } else {
                $okeys['userPersonal']['phone'] = 'ok';
            }

            if (empty($this->address)) {
                $errors['userPersonal']['address'] = Text::get('mandatory-project-field-address');
            } else {
                $okeys['userPersonal']['address'] = 'ok';
                ++$score;
            }

            if (empty($this->zipcode)) {
                $errors['userPersonal']['zipcode'] = Text::get('mandatory-project-field-zipcode');
            } else {
                $okeys['userPersonal']['zipcode'] = 'ok';
            }

            if (empty($this->location)) {
                $errors['userPersonal']['location'] = Text::get('mandatory-project-field-residence');
            } else {
                $okeys['userPersonal']['location'] = 'ok';
            }

            if (empty($this->country)) {
                $errors['userPersonal']['country'] = Text::get('mandatory-project-field-country');
            } else {
                $okeys['userPersonal']['country'] = 'ok';
            }

            /* *************** FIN Revisión del paso 2, DATOS PERSONALES **************** */

            /* *************** Revisión de campos del paso 3, DESCRIPCION **************** */
            if (empty($this->name)) {
                $errors['overview']['name'] = Text::get('mandatory-call-field-name');
            } else {
                $okeys['overview']['name'] = 'ok';
            }

            if (empty($this->subtitle)) {
                $errors['overview']['subtitle'] = Text::get('mandatory-call-field-subtitle');
            } else {
                $okeys['overview']['subtitle'] = 'ok';
            }

            if (empty($this->logo)) {
                $errors['overview']['logo'] = Text::get('mandatory-call-field-logo');
            } else {
                $okeys['overview']['logo'] = 'ok';
            }

            if (empty($this->image)) {
                $errors['overview']['image'] = Text::get('mandatory-call-field-image');
            } else {
                $okeys['overview']['image'] = 'ok';
            }

            if (empty($this->description)) {
                $errors['overview']['description'] = Text::get('mandatory-call-field-description');
            } else {
                $okeys['overview']['description'] = 'ok';
            }

            if (empty($this->whom)) {
                $errors['overview']['whom'] = Text::get('mandatory-call-field-whom');
            } else {
                $okeys['overview']['whom'] = 'ok';
            }

            if (empty($this->apply)) {
                $errors['overview']['apply'] = Text::get('mandatory-call-field-apply');
            } else {
                $okeys['overview']['apply'] = 'ok';
            }

            if (empty($this->legal)) {
                $errors['overview']['legal'] = Text::get('mandatory-call-field-legal');
            } else {
                $okeys['overview']['legal'] = 'ok';
            }

            if (empty($this->categories)) {
                $errors['overview']['categories'] = Text::get('mandatory-call-field-category');
            } else {
                $okeys['overview']['categories'] = 'ok';
            }

            if (empty($this->icons)) {
                $errors['overview']['icons'] = Text::get('mandatory-call-field-icons');
            } else {
                $okeys['overview']['icons'] = 'ok';
            }

            if (empty($this->call_location)) {
                $errors['overview']['call_location'] = Text::get('mandatory-call-field-location');
            } else {
                $okeys['overview']['call_location'] = 'ok';
            }

            if (empty($this->scope)) {
                $errors['overview']['scope'] = Text::get('mandatory-call-field-scope');
            } else {
                $okeys['overview']['scope'] = 'ok';
            }

            if (!in_array($this->modemaxp, array('imp', 'per'))) {
                $errors['overview']['modemaxp'] = Text::get('mandatory-call-field-modemaxp');
            } else {
                $okeys['overview']['modemaxp'] = 'ok';
            }

            // si no tiene presupuesto tiene que tener recursos
            if (empty($this->amount) && empty($this->resources)) {
                $errors['overview']['amount'] = Text::get('mandatory-call-field-amount');
                $errors['overview']['resources'] = Text::get('mandatory-call-field-resources');
            } else {
                $okeys['overview']['amount'] = 'ok';
                $okeys['overview']['resources'] = 'ok';
            }

            /* *************** FIN Revisión del paso 3, DESCRIPCION **************** */

            return true;
        }

        /*
         * Dinero restante
         *
         * @param id call
         */
        private function getRest($getUsed = false) {
            // cogemos la cantidad de presupuesto y la cantidad de aportes activos para esta campaña
            $sql = "
                SELECT SUM(invest.amount)
                FROM invest
                WHERE invest.campaign = 1
                AND invest.call = ?
                AND invest.status IN ('0', '1', '3')";
            $query = self::query($sql, array($this->id));
            $used = $query->fetchColumn();

            if ($getUsed)
                return $used;

            return ($this->amount - $used);
        }

        /*
         * Mira si hay que pasarla a estado exitosa
         */
        public function checkSuccess() {
            // dame los proyectos que tienen capital riego y aun no han conseguido el mínimo
            $sql = "SELECT
                            COUNT(id),
                            (SELECT  SUM(amount)
                            FROM    cost
                            WHERE   project = project.id
                            AND     required = 1
                            ) as `mincost`,
                            (SELECT  SUM(amount)
                            FROM    invest
                            WHERE   project = project.id
                            AND     invest.status IN ('0', '1', '3', '4')
                            ) as `getamount`
                    FROM project
                    WHERE project.id IN (
                        SELECT DISTINCT(invest.project)
                        FROM invest
                        WHERE invest.campaign = 1
                        AND invest.call = ?
                        AND invest.status IN ('0', '1', '3', '4')
                    )
                    HAVING getamount < mincost
                    ";
            
            $query = self::query($sql, array($this->id));
            // si alguno, nada
            // si ninguno, exitosa
            return ($query->fetchColumn() > 0) ? false : true;
        }


        /*
         * Lista de usuarios que han capturado riego de esta convocatoria
         */
        public function getSupporters($justCount = false, $user = null, $project = null) {

            $values = array(':id' => $this->id);

            // si solo contamos
            $sqlS = ($justCount) ? 'COUNT(DISTINCT(invest.user))' : 'DISTINCT(invest.user) as id';
            $sql = "SELECT $sqlS
                    FROM  invest
                    WHERE invest.call = :id
                    AND invest.status IN ('0', '1', '3')
                    AND invest.campaign IS NULL
            ";

            // si estamos filtrando cierto usuario
            if (!empty($user)) {
                $sql .= " AND invest.user = :user";
                $values[':user'] = $user;
            }

            // si estamos filtrando cierto proyecto
            if (!empty($project)) {
                $sql .= " AND invest.project = :project";
                $values[':project'] = $project;
            }

            if ($justCount) {
                $query = self::query($sql, $values);
                return $query->fetchColumn();
            } else {
                $list = array();
                $query = self::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                    $list[] = User::getMini($item->id);
                }
                return $list;
            }

        }

        // Administradores para gestión de convocatoria
        //------
        /*
         * Array asociativo de administradores de una convocatoria
         *  (o todos los que administran alguna, si no hay filtro)
         */
        public static function getAdmins ($call = null) {

            $list = array();

            $sqlFilter = "";
            if (!empty($call)) {
                $sqlFilter .= " WHERE user_call.call = '{$call}'";
            }


            $query = static::query("
                SELECT
                    DISTINCT(user_call.user) as admin,
                    user.name as name
                FROM user_call
                INNER JOIN user
                    ON user.id = user_call.user
                $sqlFilter
                ORDER BY user.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->admin] = $item->name;
            }

            return $list;
        }

        /*
         * Asignar a un usuario como administrador de un nodo
         */
		public function assign ($user, &$errors = array()) {

            $values = array(':user'=>$user, ':call'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_call (`user`, `call`) VALUES(:user, :call)";
				if (self::query($sql, $values)) {
                    ACL::allow('/translate/call/'.$this->id.'/*', '*', 'admin', $user);
    				return true;
                } else {
                    $errors[] = 'No se ha creado el registro `user_call`';
    				return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido asignar al usuario {$user} como administrador de la convocatoria {$this->id}. Por favor, revise el metodo Call->assign." . $e->getMessage();
				return false;
			}

		}

        /*
         * Quitarle a un usuario la administración de un nodo
         */
		public function unassign ($user, &$errors = array()) {
			$values = array (
				':user'=>$user,
				':call'=>$this->id,
			);

            try {
                if (self::query("DELETE FROM user_call WHERE `call` = :call AND `user` = :user", $values)) {
                    ACL::deny('/translate/call/'.$this->id.'/*', '*', 'admin', $user);
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar al usuario ' . $this->user . ' de la administracion de la convocatoria ' . $this->id . '. ' . $e->getMessage();
                return false;
			}
		}

        /**
         * Si  cierto usuario es administrador de esta convocatoria
         * @param   type varchar(50)  $id   Usuario admin
         * @return  type bool true/false
         */
        public function isAdmin ($admin) {
            $query = static::query("
                SELECT
                    `call`
                FROM user_call
                WHERE `user` = :admin
                AND `call` = :call
                LIMIT 1
                ", array(':admin' => $admin, ':call' => $this->id));

            $thecall = $query->fetchColumn();
            return ($thecall == $this->id);
        }
        
        
        

        /*
         * Estados de publicación de un convocatoria
         */

        public static function status() {
            return array(
//                0=>Text::get('form-call_status-cancelled'),
                1 => Text::get('form-call_status-edit'), // edicion
                2 => Text::get('form-call_status-review'), // en revisión
                3 => Text::get('form-call_status-apply'), // en campaña de inscripción
                4 => Text::get('form-call_status-published'), // en campaña de repartir dinero
                5 => Text::get('form-call_status-success'), // se acabo el dinero
                6 => Text::get('form-call_status-expired'));          // la hemos cancelado
        }

        public static function blankErrors() {
            // para guardar los fallos en los datos
            $errors = array(
                'userProfile' => array(), // Errores en el paso 1
                'userPersonal' => array(), // Errores en el paso 2
                'overview' => array(),   // Errores en el paso 3
                'supports' => array()   // Errores en el paso 4
            );

            return $errors;
        }

    }

}