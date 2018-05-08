<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model;

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;
use Goteo\Application\Exception;
use Goteo\Library\Text;

class Node extends \Goteo\Core\Model {

    public
        $id = null,
        $name,
        $subtitle,
        $description,
        $email,
        $admins = array(), // administradores
        $logo,
        $sello,
        $home_img,
        $active,
        $image,
        $default_consultant,
        $sponsors_limit;


    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);

        if($this->id) {
            // y sus administradores
            $this->admins = self::getAdmins($this->id);
            // pojects

            $this->summary = $this->getSummary();

            // logo
            $this->logo = (!empty($this->logo)) ? Image::get($this->logo) : null;

            // label
            $this->label = (!empty($this->label)) ? Image::get($this->label) : null;

            // home img
            $this->home_img = (!empty($this->home_img)) ? Image::get($this->home_img) : $this->logo;
        }

    }

    public static function getLangFields() {
        return ['subtitle', 'description'];
    }

    /**
     * Obtener datos de un nodo
     * @param   type mixed  $id     Identificador
     * @return  type object         Objeto
     */
    static public function get ($id, $lang = null) {

        //Obtenemos el idioma de soporte
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                node.id as id,
                node.name as name,
                node.email as email,
                $fields,
                node.logo as logo,
                node.label as label,
                node.home_img as home_img,
                node.location as location,
                node.url as url,
                node.active as active,
                node.twitter as twitter,
                node.facebook as facebook,
                node.linkedin as linkedin,
                node.google as google,
                node.owner_background as owner_background,
                node.owner_font_color as owner_font_color,
                node.owner_social_color as owner_social_color,
                node.default_consultant as default_consultant,
                node.sponsors_limit as sponsors_limit
            FROM node
            $joins
            WHERE node.id = :id";

        $values = [':id' => $id];
        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        $item = $query->fetchObject(__CLASS__);

        if (!$item instanceof Node) {
            throw new Exception\ModelNotFoundException(Text::get('fatal-error-node'));
        }

        return $item;
    }

    static public function getMini ($id) {
            $sql = static::query("
                SELECT
                    id, name, url, email
                FROM node
                WHERE id = :id
                ", array(':id' => $id));
            $item = $sql->fetchObject();

            return $item;
    }

    /*
     * Array asociativo de administradores de un nodo
     *  (o todos los que administran si no hay filtro)
     */
    public static function getAdmins ($node = null) {

        $list = array();

        $sqlFilter = " WHERE user_role.role_id IN ('admin', 'superadmin')";
        if ($node) {
            $sqlFilter .= " AND user_role.node_id = ?";
        }


        $query = static::query("
            SELECT
                DISTINCT(user_role.user_id) as admin,
                user.name as name
            FROM user_role
            INNER JOIN user
                ON user.id = user_role.user_id
            $sqlFilter
            ORDER BY user.name ASC
            ", array($node));

        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
            $list[$item->admin] = $item->name;
        }

        return $list;
    }

    /*
     * Lista de nodos
     */
    public static function getAll ($filters = array(), $lang = null) {

        $sqlFilter = [];
        $values = [];

        if (!empty($filters['global'])) {
            $sqlFilter[] = "(node.id LIKE :global OR node.name LIKE :global OR node.subtitle LIKE :global)";
            $values[':global'] = '%' . $filters['global'] . '%';
        }

        if (!empty($filters['name'])) {
            $sqlFilter[] = "( node.name LIKE :name OR node.id = :id )";
            $values[':name'] = '%' . $filters['name'] . '%';
            $values[':id'] = $filters['name'];
        }

        if (!empty($filters['type'])) {
                if($filters['type'] == 'channel')
                    $sqlFilter[] = "node.url = ''";
                else
                    $sqlFilter[] = "node.url != ''";
        }

        if (!empty($filters['status'])) {
            $active = $filters['status'] == 'active' ? '1' : '0';
            $sqlFilter[] = "node.active = '$active'";
        }

        if (!empty($filters['admin'])) {
            $sqlFilter[] = "node.id IN (SELECT node_id FROM user_role WHERE user_id = :user)";
            $values[':user'] = $filters['admin'];
        }

        if (isset($filters['available'])) {
            if($filters['available']) {
                $sqlFilter[] = "(node.active=1 OR node.id IN (SELECT node_id FROM user_role WHERE user_id = :user))";
                $values[':user'] = $filters['available'];
            } else {
                $sqlFilter[] = "node.active=1";
            }
        }

        if($sqlFilter) $sqlFilter = ' WHERE '. implode(' AND ', $sqlFilter);
        else $sqlFilter = '';

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT node.id,
                       node.name,
                       $fields,
                       node.email,
                       node.active,
                       node.url,
                       node.logo,
                       node.location,
                       node.twitter,
                       node.facebook,
                       node.google,
                       node.linkedin,
                       node.label,
                       node.owner_background,
                       node.default_consultant,
                       node.sponsors_limit,
                       node.home_img,
                       node.owner_font_color,
                       node.owner_social_color,
                       :viewLang as viewLang

        FROM node
        $joins
        $sqlFilter
        ORDER BY node.name ASC";
        // die(\sqldbg($sql, $values));
        if($query = static::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    /*
     * Lista simple de nodos
     * TODO: complete this with paginations, filters and counter
     */
    public static function getList () {

        $list = array();

        $query = static::query("
            SELECT
                id,
                name
            FROM node
            ORDER BY name ASC
            ");

        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
            $list[$item->id] = $item->name;
        }

        return $list;
    }

    /**
     * Validar.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate (&$errors = array()) {
        if (empty($this->id)) {
            $errors[] = 'Falta Identificador';
        }

        if(preg_match('/[^a-z0-9]/',$this->id) || strlen($this->id) > 50) {
            $errors[] = "Newid [$this->id] is not valid or too long, please use only lowercase characters!";
        }

        if (empty($this->name)) {
            // $errors[] = 'Empty name';
            $this->name = $this->id;
        }

        if (empty($this->email)) {
            $this->email = Config::get('mail.mail');
        }

        if (!isset($this->active)) {
            $this->active = 0;
        }

        if (isset($this->logo->id)) {
            $this->logo = $this->logo->id;
        }

        if (isset($this->label->id)) {
            $this->label = $this->label->id;
        }

        if (isset($this->home_img->id)) {
            $this->home_img = $this->home_img->id;
        }

        return empty($errors);
    }

    public function getHomeImage() {
        if(!$this->homeImageInstance instanceOf Image) {
            $this->homeImageInstance = new Image($this->home_img);
        }
        return $this->homeImageInstance;
    }

    public function getLogo() {
        if(!$this->logoInstance instanceOf Image) {
            $this->logoInstance = new Image($this->logo);
        }
        return $this->logoInstance;
    }

    public function getLabel() {
        if(!$this->labelInstance instanceOf Image) {
            $this->labelInstance = new Image($this->label);
        }
        return $this->labelInstance;
    }

    /**
	 * Guardar.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
     public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'name',
            'email',
            'active',
            'subtitle',
            'description',
            'url',
            'default_consultant',
            'sponsors_limit'
            );

        $set = '';
        $values = array(':id' => $this->id);

        foreach ($fields as $field) {
            if ($set != '') $set .= ", ";
            if($field === 'default_consultant' && empty($this->default_consultant)) {
                $set .= "`$field` = NULL ";
                continue;
            }

            $set .= "`$field` = :$field ";
            $values[":$field"] = (string)$this->$field;
        }

        try {
            $sql = "UPDATE node SET " . $set . " WHERE id = :id";

            self::query($sql, $values);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "HA FALLADO!!! " . $e->getMessage();
            return false;
        }
     }

    /**
	 * Guarda lo imprescindible para crear el registro.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
     public function create (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'name',
            'email',
            'subtitle',
            'description',
            'url',
            'active',
            'default_consultant',
            'sponsors_limit'
            );

        $set = '';
        $values = array();

        foreach ($fields as $field) {
            if ($set != '') $set .= ", ";
            if($field === 'default_consultant' && empty($this->default_consultant)) {
                $set .= "`$field` = NULL ";
                continue;
            }
            $set .= "`$field` = :$field ";
            $values[":$field"] = (string)$this->$field;
        }

        try {
            $sql = "INSERT INTO node SET " . $set;
            self::query($sql, $values);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Error creating: " . $e->getMessage();
        }
        return false;
     }

     /**
      * Renames a id
      * @param  string $newid [description]
      */
     public function rebase($newid) {
        if(preg_match('/[^a-z0-9]/',$newid) || strlen($newid) > 50) {
            throw new Exception\ModelException("Newid [$newid] is not valid or too long, please use only lowercase characters!");
        }
        try {
            $sql = "UPDATE node SET id = :newid WHERE id = :oldid";
            self::query($sql, [':oldid' => $this->id, ':newid' => $newid]);
        } catch(\PDOException $e) {
            throw new Exception\ModelException("Error rebasing node [{$this->id}] to [$newid]" . $e->getMessage());
        }

        $this->id = $newid;
        return true;
     }

    public function getUrl() {
        $url = Config::get('url.main');
        if( ! $this->isMasterNode()) {
            $url = ($this->url ? $this->url : $url . '/channel/' . $this->id);
        }
        return $url;
    }
    /**
    * Returns true if is the master node
    * @return boolean [description]
    */
    public function isMasterNode() {
        return Config::isMasterNode($this->id);
    }

    /*
     * Asignar a un usuario como administrador de un nodo
     */
	public function assign ($user, &$errors = array()) {

        $values = array(':user'=>$user, ':node'=>$this->id);

		try {
            $sql = "REPLACE INTO user_role (`user_id`, `role_id`, `node_id`) VALUES(:user, 'admin', :node)";
			if (self::query($sql, $values)) {
				return true;
            } else {
                $errors[] = 'No se ha creado el registro `user_role`';
				return false;
            }
		} catch(\PDOException $e) {
			$errors[] = "No se ha podido asignar al usuario {$user} como administrador del nodo {$this->id}. Por favor, revise el metodo Node->assign." . $e->getMessage();
			return false;
		}

	}

    /*
     * Quitarle a un usuario la administración de un nodo
     */
	public function unassign ($user, &$errors = array()) {
		$values = array (
			':user'=>$user,
			':node'=>$this->id,
		);

        try {
            if (self::query("DELETE FROM user_role WHERE node_id = :node AND `role_id` = 'admin' AND user_id = :user", $values)) {
                return true;
            } else {
                return false;
            }
		} catch(\PDOException $e) {
            $errors[] = 'No se ha podido quitar al usuario ' . $this->user . ' de la administracion del nodo ' . $this->id . '. ' . $e->getMessage();
            return false;
		}
	}

    /*
     * Para actualizar los datos de descripción
     */
     public function update (&$errors) {
         if (empty($this->id)) return false;

        // Primero tratamos la imagen
        if (is_array($this->logo) && !empty($this->logo['name'])) {
            $image = new Image($this->logo);

            if ($image->save($errors)) {
                $this->logo = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->logo = '';
            }
        }
        if (is_null($this->logo)) {
            $this->logo = '';
        }

        // Tratamos el sello
        if (is_array($this->label) && !empty($this->label['name'])) {
            $image = new Image($this->label);

            if ($image->save($errors)) {
                $this->label = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->label = '';
            }
        }
        if (is_null($this->label)) {
            $this->label = '';
        }

        // Tratamos imagen módulo home
        if (is_array($this->home_img) && !empty($this->home_img['name'])) {
            $image = new Image($this->home_img);

            if ($image->save($errors)) {
                $this->home_img = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->home_img = '';
            }
        }
        if (is_null($this->home_img)) {
            $this->home_img = '';
        }

        $fields = array(
            'name',
            'subtitle',
            'email',
            'location',
            'logo',
            'label',
            'home_img',
            'description',
            'twitter',
            'facebook',
            'google',
            'linkedin',
            'owner_background',
            'owner_font_color',
            'owner_social_color'
            );

        $values = array (':id' => $this->id);

        foreach ($fields as $field) {
            if ($set != '') $set .= ", ";
            $set .= "`$field` = :$field ";
            $values[":$field"] = $this->$field;
        }

        try {
            if ($set != '') {
                $sql = "UPDATE node SET " . $set ." WHERE id = :id";
                self::query($sql, $values);
            }

            return true;
        } catch(\PDOException $e) {
            $errors[] = "HA FALLADO!!! " . $e->getMessage();
            return false;
        }
     }

    /*
     * Para actualizar la traduccion
     */
     public function updateLang (&$errors) {
         if (empty($this->id)) return false;

			try {
            $fields = array(
                'id'=>'id',
                'lang'=>'lang_lang',
                'subtitle'=>'subtitle_lang',
                'description'=>'description_lang'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field=>$ffield) {
                if ($set != '') $set .= ', ';
                $set .= "$field = :$field";
                $values[":$field"] = $this->$ffield;
            }

			$sql = "REPLACE INTO node_lang SET " . $set;
			if (self::query($sql, $values)) {
                return true;
            } else {
                $errors[] = $sql . '<pre>' . print_r($values, true) . '</pre>';
                return false;
            }
		} catch(\PDOException $e) {
            $errors[] = 'Error sql al grabar la traduccion del nodo.' . $e->getMessage();
            return false;
		}

     }

    /**
     * Saca una lista de nodos disponibles para traducir
     *
     * @param array filters
     * @param string node id
     * @return array of project instances
     */
    public static function getTranslates($filters = array()) {
        $list = array();

        $values = array();

        $and = " WHERE";
        $sqlFilter = "";
        if (!empty($filters['admin'])) {
            $sqlFilter .= "$and id IN (
                SELECT node
                FROM user_node
                WHERE user = :admin
                )";
            $and = " AND";
            $values[':admin'] = $filters['admin'];
        }
        if (!empty($filters['translator'])) {
            $sqlFilter .= "$and id IN (
                SELECT item
                FROM user_translate
                WHERE user = :translator
                AND type = 'node'
                )";
            $and = " AND";
            $values[':translator'] = $filters['translator'];
        }

        $sql = "SELECT
                    id
                FROM `node`
                $sqlFilter
                ORDER BY name ASC
                ";

        $query = self::query($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $item) {
            $anode = self::get($item['id']);
            $anode->translators = \Goteo\Model\User\Translate::translators($item['id'], 'node');
            $list[] = $anode;
        }
        return $list;
    }



    /** Resumen proyectos: (asignados a este nodo)
     * total proyectos,
     * activos (en campaña),
     * exitosos (que han llegado al mínimo),
     * cofinanciadores (diferentes),
     * colaboradores (diferentes)
     * total de dinero recaudado
     *
     * @return array asoc
     */
    public function getSummary () {

        // sacamos registro de la tabla de calculos
        $sql = "
            SELECT
                projects,
                active,
                success,
                investors,
                supporters,
                amount,
                unix_timestamp(now()) - unix_timestamp(updated) as timeago
            FROM node_data
            WHERE node = :node
            LIMIT 1
            ";
        try {
            $query = self::query($sql, array(':node' => $this->id));
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            // si el calculo tiene más de 30 minutos (ojo, timeago son segundos) , calculamos de nuevo
            if (empty($data) || $data['timeago'] > (30*60)) {
                if ($newdata = $this->updateData()) {
                    return $newdata;
                }
            }
            return $data;
        } catch(\PDOException $e) {

        }
        return [];
    }

    /** Resumen convocatorias: (destacadas por el nodo)
     * nº campañas abiertas
     * nº convocatorias activas
     * importe total de las campañas
     * resto total
     *
     * @return array asoc
     */
    public function getSumcalls () {

        // sacamos registro de la abla de calculos
        $sql = "
            SELECT
                budget,
                rest,
                calls,
                campaigns,
                unix_timestamp(now()) - unix_timestamp(updated) as timeago
            FROM node_data
            WHERE node = :node
            LIMIT 1
            ";
        $query = self::query($sql, array(':node' => $this->id));
        $data = $query->fetch(\PDO::FETCH_ASSOC);

        // si el calculo tiene más de 30 minutos (ojo, timeago son segundos) , calculamos de nuevo
        if (empty($data) || $data['timeago'] > (30*60)) {
            if ($newdata = $this->updateData()) {
                return $newdata;
            }
        }

        return $data;
    }

    /**
     * Check if the channel can be previewed by the user id
     * @param  Goteo\Model\User $user  the user to check (if empty checks )
     * @return boolean          true if success, false otherwise
      */
    public function userCanView(User $user = null) {

    // is admin in the node or the node is active
    if($user->hasRoleInNode($this->id, ['admin', 'superadmin', 'root'])) return true;

    return false;

    }

    private function updateData () {
        $values = array(':node' => $this->id);
        $data = array();

        // primero calculamos y lo metemos tanto en values como en data
        // datos de proyectos
        // nº de proyectos
        $query = static::query("
            SELECT
                COUNT(project.id)
            FROM    project
            WHERE node = :node
            AND status IN (3, 4, 5, 6)
            ", $values);
        $data['projects'] = $query->fetchColumn();

        // proyectos activos
        $query = static::query("
            SELECT
                COUNT(project.id)
            FROM    project
            WHERE node = :node
            AND status = 3
            ", $values);
        $data['active'] = $query->fetchColumn();

        // proyectos exitosos
        // ojo! hay que tener en cuenta los que llegan al mínimo
        $query = static::query("
            SELECT
                project.id,
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
            FROM    project
            WHERE node = :node
            AND status IN ('3', '4', '5')
            HAVING getamount >= mincost
            ", $values);
        $data['success'] = $query->rowCount();

        // cofinanciadores
        $query = static::query("
            SELECT
                COUNT(DISTINCT(invest.user))
            FROM  invest
            INNER JOIN project
                ON project.id = invest.project
            WHERE project.node = :node
            AND invest.status IN ('0', '1', '3', '4')
            ", $values);
        $data['investors'] = $query->fetchColumn();

        // colaboradores (que han enviado algun mensaje)
        $query = static::query("
            SELECT
                COUNT(DISTINCT(message.user))
            FROM  message
            INNER JOIN project
                ON project.id = message.project
            WHERE project.node = :node
            AND message.user != project.owner
            ", $values);
        $data['supporters'] = $query->fetchColumn();

        // cantidad recaudada en total
        $query = static::query("
            SELECT
                SUM(invest.amount)
            FROM  invest
            INNER JOIN project
                ON project.id = invest.project
            WHERE project.node = :node
            AND invest.status IN ('0', '1', '3')
            ", $values);
        $data['amount'] = $query->fetchColumn();

        // datos de convocatorias (destacadas por el nodo)
        // presupuesto
        $query = static::query("
            SELECT
                SUM(amount)
            FROM    `call`
            INNER JOIN campaign
                ON call.id = campaign.call
                AND node = :node
            ", $values);
        $data['budget'] = $query->fetchColumn();

        // por repartir
        $query = static::query("
            SELECT SUM(invest.amount)
            FROM invest
            INNER JOIN campaign
                ON invest.call = campaign.call
                AND node = :node
            WHERE invest.campaign = 1
            AND invest.status IN ('0', '1', '3')
            ", $values);
        $data['rest'] = $data['budget'] - $query->fetchColumn();

        // proyectos activos
        $query = static::query("
            SELECT
                COUNT(call.id)
            FROM    `call`
            INNER JOIN campaign
                ON call.id = campaign.call
                AND node = :node
            WHERE call.status = 3
            ", $values);
        $data['calls'] = $query->fetchColumn();

        // proyectos activos
        $query = static::query("
            SELECT
                COUNT(call.id)
            FROM   `call`
            INNER JOIN campaign
                ON call.id = campaign.call
                AND node = :node
            WHERE call.status = 4
            ", $values);
        $data['campaigns'] = $query->fetchColumn();



        //grabamos los datos en la tabla
        $set = 'node = :node';

        $fields = array(
            'projects',
            'active',
            'success',
            'investors',
            'supporters',
            'amount',
            'budget',
            'rest',
            'calls',
            'campaigns'
            );

        foreach ($fields as $field) {
            $set .= ', ';
            $set .= "$field = :$field";
            $values[":$field"] = $data[$field];
        }

        $sql = "REPLACE node_data SET " . $set;
        if (self::query($sql, $values)) {
            // devolvemos los datos
            return $data;
        } else {
            return false;
        }

    }

}

