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

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\App;
use Symfony\Component\Yaml\Yaml;

/*
 * Clase para gestionar las plantillas de los emails automáticos
 */
class Template extends \Goteo\Core\Model {

    const MESSAGE_CONTACT = 1;     // Mensaje de contacto
    const MESSAGE_DONORS = 2;     // Mensaje a los cofinanciadores
    const MESSAGE_OWNER = 3;     // Mensaje al autor
    const MESSAGE_USERS = 4;     // Mensaje entre usuarios
    const CONFIRM_REGISTER = 5;     // Confirmación de registro
    const RETRIEVE_PASSWORD = 6;     // Recuperar contraseña
    const EMAIL_CHANGE = 7;     // Cambio de email
    const PROJECT_SENT_CONFIRM = 8;     // Confirmacion de proyecto enviado
    const UNSUBSCRIBE = 9;     // Darse de baja
    const INVEST_THANKS = 10;    // OBSOLETA Agradecimiento aporte
    const ADMIN_MESSAGE = 11;    // Comunicación desde admin
    const THREAD_OWNER = 12;    // Mensaje al autor de un thread
    const ADVISE_8_DAYS_TO_FAIL = 13;    // Aviso de 8 días para fallar
    const ADVISE_2_DAYS_TO_FAIL = 14;    // Aviso de 2 días para fallar
    const DONORS_PROJECT_1_ROUND = 15;    // Agradecimiento cofinanciadores si supera primera
    const DONORS_PROJECT_2_ROUND = 16;    // Agradecimiento cofinanciadores final segunda
    const DONORS_PROJECT_FAILED = 17;    // Aviso cofinanciadores proyecto fallido
    const DONORS_PROJECT_NEWS = 18;    // Aviso cofinanciadores novedade en proyecto
    const ADVISE_25_DAY = 19;    // Difusión día 25
    const OWNER_PROJECT_1_ROUND = 20;    // Notificación al autor proyecto supera primera ronda
    const OWNER_PROJECT_FAILED = 21;    // Notificación al autor proyecto fallido
    const OWNER_PROJECT_2_ROUND = 22;    // Notificación al autor proyecto fin segunda ronda
    const ADVISE_16_DAYS = 23;    // Difusión día 16 (y cada semana)
    const ADVISE_6_DAYS = 24;    // Difusión día 6 (y cada 6 días)
    const ADVISE_2_MONTHS = 25;    // Recuerdo 2 meses después
    const READY_FOR_TRANSLATING = 26;    // Informa al autor de proyecto listo para traducción
    const DONORS_WARNING = 27;    // Aviso a los donantes
    const DONORS_THANKS = 28;    // OBSOLETA Agradecimiento donativo
    const OWNER_NEW_INVEST = 29;    // Notificación de nuevo aporte al autor
    const OWNER_NEW_THREAD = 30;    // Notificacion nuevo thread
    const OWNER_NEW_COMMENT = 31;    // Notificación comentario en novedades
    const OWNER_CALL_READY_FOR_TRANSLATING = 32;    // Informa al autor de convocatoria lista para traducción
    const NEWSLETTER = 33;    // Boletin
    const INVEST_THANKS_2_ROUND = 34;    // OBSOLETA Agradecimiento aporte segunda ronda
    const TEST = 35;    // Para testeo
    const DONORS_THANKS_2_ROUND = 36;    // OBSOLETA Agradecimiento donativo segunda ronda
    const PAYPAL_ISSUE = 37;    // Aviso incidencia PayPal
    const DONORS_REMINDER = 38;    // Recordatorio donantes
    const CALL_CONFIRMATION = 39;    // Confirmación de aplicación a convocatoria
    const PROJECT_EXPRESS_DISCARD = 40;    // Rechazo express
    const ADVISE_1_DAYS = 41;    // Difusión día 1
    const ADVISE_2_DAYS = 42;    // Difusión día 2
    const ADVISE_3_DAYS = 43;    // Difusión día 3
    const ADVISE_4_DAYS = 44;    // Difusión día 4
    const ADVISE_5_DAYS = 45;    // Difusión día 5
    const ADVISE_7_DAYS = 46;    // Difusión día 7 (si tiene más de 20 cofinanciadores)
    const ADVISE_8_DAYS = 47;    // Difusión día 8
    const ADVISE_9_DAYS = 48;    // Difusión día 9
    const ADVISE_10_DAYS = 49;    // Difusión día 10
    const ADVISE_11_DAYS = 50;    // Difusión día 11 (si no tiene video motivacional)
    const ADVISE_15_DAYS = 51;    // Difusión día 15 (si no ha llegado al mínimo)
    const OWNER_REWARD_REMINDER = 52;    // Recuerdo de gestionar retornos (8 meses después)
    const BAZAAR_RECEIVER = 53;    // Mensaje al destinatario de un regalo del bazaar
    const PROJECT_FAILED_RECEIVERS = 54;    // Aviso proyecto fallido para el que espera regalo
    const CONTRACT = 55;    // Datos contrato
    const CONSULTANT_PROJECT_REWARD_FAIL = 56;    // Aviso proyecto no cumplidor
    const ADVISE_0_DAYS = 57;    // Mensaje día 0
    const CONSULTANT_PROJECT_PUBLIC_REWARD_FILL = 58;    // Proyecto rellena campo retorno colectivo
    const CONSULTANT_PROJECT_VALIDATE = 59;    // Proyecto enviado a valoración
    const OWNER_PROJECT_SINGLE_ROUND = 60;    // Notificación al autor proyecto fin ronda unica
    const DONORS_PROJECT_SINGLE_ROUND = 61;    // Agradecimiento cofinanciadores al final de ronda única
    const OWNER_PROJECT_REVIEWED = 62;    // Confirmacion de proyecto revisado
    const CONSULTANT_PROJECT_SENT = 63;    // Proyecto enviado desde preform a valoración
    const DONOR_INVEST_THANKS = 64;    // Agradecimiento aporte
    const POOL_RECHARGUE_THANKS = 65;    // Agradecimiento aporte
    const FAVOURITE_PROJECT_REMEMBER = 66;    // Recordatorio favorito
    const CONTACT_AUTO_REPLY_ACCOUNT_PROBLEMS = 67;    // Acount problems
    const CONTACT_AUTO_REPLY_NEW_PROJECT = 68;    // New project chance
    const CONTACT_AUTO_REPLY_PROJECT_FORM = 69;    // Queries about the project form
    const CONTACT_AUTO_REPLY_DEV = 70;    // Dev
    const CONTACT_AUTO_REPLY_RELIEF = 71;    // Relief
    const COMMUNICATION = 72;    // Multi-purpose communication
    const PROJECT_CREATED = 73;    // Project created
    const MATCHER_PROJECT_ADDED = 'matcher_project_added';    // inform to owner that a new project is added to a matcher
    const MATCHER_PROJECT_ACTIVATED = 'matcher_project_activated';    // inform to owner that a new project is activated in a matcher
    const MATCHER_PROJECT_DISCARDED = 'matcher_project_discarded';    // inform to owner that a new project is discarded from a matcher
    const MATCHER_PROJECT_ADDED_ADMIN = 'matcher_project_added_admin';    // inform to admin that a new project is added to a matcher
    const PROJECT_DERIVATION_DISCARD = 'project_derivation_discard';    // Discard derivating to others platforms.

    public
        $id,
        $lang,
        $name,
        $purpose,
        $title,
        $group,
        $type,
        $text;

    static public function getFieldLangs() {
        return ['title', 'test'];
    }

    static public function get ($id, &$lang = null, $avoid_pending = true) {

        //Obtenemos el idioma de soporte
        $lang=static::default_lang_by_id($id, 'template_lang', $lang);

        // buscamos la plantilla en ese idioma
		$sql = "SELECT  template.id as id,
                        template.name as name,
                        template.group as `group`,
                        template_lang.lang as `lang`,
                        template.purpose as purpose,
                        template.type as type,
                        IFNULL(template_lang.title, template.title) as title,
                        IFNULL(template_lang.text, template.text) as text
                 FROM template
                 LEFT JOIN template_lang
                    ON  template_lang.id = template.id
                    AND template_lang.lang = :lang
                    " . ($avoid_pending ? ' AND template_lang.pending=0' : '') . "
                 WHERE template.id = :id
                ";
        $values = array( ':id' => $id, ':lang' => $lang );
        // die(\sqldbg($sql, $values));
		if($query = static::query($sql, $values)) {
		  if($template = $query->fetchObject(__CLASS__)) {
            return $template;
          }
        }
        // search default from yaml
        return static::getDefault($id, $lang);
    }

    /**
     * Finds a template from the filesystem defaults (YAML format)
     * @param  [type] $id    [description]
     * @param  [type] &$lang [description]
     * @return [type]        [description]
     */
    static public function getDefault($id, &$lang = null) {
        $fallback = Lang::getFallback($lang);

        // Find the right template
        foreach([$lang, $fallback] as $lang) {
            $file = GOTEO_PATH . "Resources/mailing/$lang/$id.yml";
            if(is_file($file)) {
                $yaml = Yaml::parse(file_get_contents($file));
                $yaml['id'] = $id;
                $yaml['lang'] = $lang;
                return new self($yaml);
            }
        }

        throw new ModelNotFoundException('Not found template [' . $id . ']');
    }

	/*
	 *  Metodo para la lista de páginas
	 */
	public static function getAll($filters = array()) {
        $templates = array();

        try {

            $values = array(':lang' => Lang::current());
            $sqlFilter = '';
            $and = "WHERE";
            if (!empty($filters['id'])) {
                $sqlFilter .= " $and template.`id` = :id";
                $and = "AND";
                $values[':id'] = "{$filters['id']}";
            }
            if (!empty($filters['group'])) {
                $sqlFilter .= " $and template.`group` = :group";
                $and = "AND";
                $values[':group'] = "{$filters['group']}";
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " $and (template.`name` LIKE :name OR template.`purpose` LIKE :name OR template.`title` LIKE :name)";
                $and = "AND";
                $values[':name'] = "%{$filters['name']}%";
            }

            $sql = "SELECT
                        template.id as id,
                        template.name as name,
                        template.purpose as purpose,
                        template.type as type,
                        IFNULL(template_lang.title, template.title) as title,
                        IFNULL(template_lang.text, template.text) as text
                    FROM template
                    LEFT JOIN template_lang
                        ON  template_lang.id = template.id
                        AND template_lang.lang = :lang
                    $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = static::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $template) {
                $templates[] = $template;
            }
            return $templates;
        } catch (\PDOException $e) {
            throw new ModelException('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
        }
	}

	/*
	 *  Lista de plantillas para filtro
	 */
	public static function getAllMini() {
        $templates = array();

        try {
            $sql = "SELECT
                        template.id as id,
                        template.name as name,
                        template.type as type
                    FROM template
                    ORDER BY name ASC
                    ";

            $query = static::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $template) {
                $templates[$template->id] = $template->name;
            }
            return $templates;
        } catch (\PDOException $e) {
            throw new ModelException('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
        }
	}

    /**
     * creates the html if is markdown
     *
     * @return string HTML content
     */
    public function parseText() {
        if($this->type === 'md') {
            return App::getService('app.md.parser')->text($this->text);
        }
        return $this->text;
    }

    public function validate(&$errors = array()) {

        $allok = true;

        if (empty($this->id)) {
            $errors[] = 'Registro sin id';
            $allok = false;
        }

        if (empty($this->title)) {
            $errors[] = 'Registro sin titulo';
            $allok = false;
        }

        if (empty($this->text)) {
            $errors[] = 'Registro sin contenido';
            $allok = false;
        }

        if (!in_array($this->type, ['plain', 'html', 'md'])) {
            $errors[] = 'Non valid text type';
            $allok = false;
        }

        return $allok;
    }

	/*
	 *  Esto se usara para la gestión de contenido
	 */
	public function save(&$errors = array()) {
        if($this->validate($errors)) {
			try {
                $this->dbInsertUpdate(['name', 'group', 'purpose', 'title', 'text', 'type']);
                return true;
    		} catch(\PDOException $e) {
                $errors[] = 'Template saving error! ' . $e->getMessage();
            }
        }
        return false;
	}

    /**
     * Returns available langs for a template
     */
    static public function getAvailableLangs($id, $avoid_pending = true) {
        $query = self::query('SELECT DISTINCT lang FROM template_lang WHERE ' . ($avoid_pending ? ' pending=0 AND ' : '') . ' id=?', $id);
        $langs = [Lang::getDefault()]; // add system lang
        if($query) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $lang) {
                $langs[] = $lang->lang;
            }
        }
        return $langs;
    }
    /*
     * Grupos de textos
     */
    static public function groups()
    {
        $groups = array(
            'general' => 'Propósito general',
            'massive' => 'Masivos',
            'access'  => 'Registro y acceso usuario',
            'project' => 'Actividad proyecto',
            'tips'    => 'Auto-tips difusión',
            'invest'  => 'Proceso aporte',
            'contact' => 'Comunicación',
            'advice'  => 'Avisos al autor',
            'contract'=> 'Proceso contrato'
        );

        \asort($groups);

        return $groups;
    }

}
