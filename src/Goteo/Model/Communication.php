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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Model\Image;

class Communication extends \Goteo\Core\Model {

    public
        $id,
        $subject,
        $content,
        $header,
        $type,
        $template = null,
        $sent = 0,
        $error = '',
        $lang,
        $date,
        $projects,
        $filter;

    public static function getLangFields() {
        return ['subject', 'content'];
    }


    static public function get($id) {
		if (empty($id)) {
			throw new Exception("Delete error: ID not defined!");
		}
		$class = get_called_class();
		$ob = new $class();
		$query = static::query('SELECT * FROM ' . $ob->getTable() . ' WHERE id = :id', array(':id' => $id));
        $communication = $query->fetchObject($class);

        if (!$communication instanceof Communication) {
            throw new ModelNotFoundException("Not found communication [$id]");
        }
        $communication->projects = self::getCommunicationProjects($communication->id);

        return $communication;
	}

    /**
     * Validar mensaje.
     * @param type array	$errors
     */
	public function validate(&$errors = array()) {
	    if(empty($this->content)) {
	        $errors['content'] = 'The communication has no content';
	    }
        if(empty($this->subject)) {
            $errors['subject'] = 'The communication has no subject';
        }
        if($this->template == Template::NEWSLETTER && empty($this->header)) {
            $errors['header'] = 'The newsletter has no header';
        }
        return empty($errors);
    }

    /**
     * Communication listing
     *
     * @param array filters
     * @param int offset items
     * @param int limit items per page or 0 for unlimited
     * @param int count the number of instances
     * @return array of communication instances or the number of instances if count == true
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        if(!$lang) $lang = Lang::current();
        $values = [];
        $sqlFilters = [];
        $sql = '';

        foreach(['type', 'template'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "communication.$key = :$key";
                $values[":$key"] = $filters[$key];
            }
        }

        if(isset($filters['id'])) {
            $filter[] = "communication.id = :id";
            $values[":id"] = '%' . $filters['id'] . '%';
        }
        if(isset($filters['subject'])) {
            $filter[] = "communication.subject LIKE :subject";
            $values[":subject"] = '%' . $filters['subject'] . '%';
        }
        if(isset($filters['filter'])) {
            $filter[] = "communication.filter LIKE :filter";
            $values[":filter"] = '%' . $filters['filter'] . '%';
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM communication$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        // $values['lang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql ="SELECT
                communication.id as id,
                communication.type as type,
                $fields,
                communication.lang as lang,
                communication.header as header,
                communication.template as template,
                communication.date as date,
                communication.filter as filter
            FROM communication
            $joins
            $sql
            ORDER BY `id` DESC
            LIMIT $offset,$limit";

        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    public function setPromotedProjects() {
        $values = Array(':communication' => $this->id, ':project' => '');

        try {
            $query = static::query('DELETE FROM communication_project WHERE communication = :communication', Array(':communication' => $this->id));
        }
        catch (\PDOException $e) {
            Message::error("Error deleting previous communication projects for communication " . $this->id . " " . $e->getMessage());
        }

        foreach($this->projects as $key => $value) {
            $values[':project'] = $value;
            $values[':order'] = $key;
            try {
                if ($value)
                    $query = static::query('INSERT INTO communication_project(`communication`, `project`, `order`) VALUES(:communication,:project,:order)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter projects " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    static public function getCommunicationProjects ($communication){
        $query = static::query('SELECT `project` FROM communication_project WHERE `communication` = ? ORDER BY `order` ASC', $communication);
        $projects = $query->fetchAll(\PDO::FETCH_ASSOC);

        $communication_projects = [];

        foreach($projects as $project) {
            foreach($project as $key => $value) {
                $project = Project::getMini($value);
                $project->image = Image::get($project->image);
                $communication_projects[] = $project;
            }
        }
        return $communication_projects;
    }

    static public function getCommunicationProjectsMini ($communication){
        $query = static::query('SELECT `project` FROM communication_project WHERE communication = ?', $communication);
        $projects = $query->fetchAll(\PDO::FETCH_ASSOC);

        $communication_projects = [];

        foreach($projects as $project) {
            foreach($project as $key => $value) {
                $project = Project::getMini($value);
                $communication_projects[] = $project;
            }
        }
        return $communication_projects;
    }

    public function save(&$errors = []) {
        $this->validate($errors);
        if( !empty($errors) ) return false;

        $fields = array(
            'id',
            'subject',
            'content',
            'header',
            'type',
            'template',
            'lang',
            'filter'
        );

        try {
            $this->dbInsertUpdate($fields);

            $this->setPromotedProjects();
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving email to database: ' . $e->getMessage();
        }

        return false;

    }

    public function getImage() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->header);
        }
        return $this->imageInstance;
    }


    public function getOriginalLang(){
        return $this->lang;
    }

    public static function variables () {
        return array(
            'userid' => Text::get('admin-communications-userid-content'),
            'useremail' => Text::get('admin-communications-useremail-content'),
            'username' => Text::get('admin-communications-username-content'),
            'siteurl' => Text::get('admin-communications-siteurl-content', ['%SITEURL%' => SITE_URL]),
            'subscribeurl' => Text::get('admin-communications-subscribeurl-content'),
            'unsubscribeurl' => Text::get('admin-communications-unsubscribeurl-content'),
            'poolamount' => Text::get('admin-communications-poolamount-content')
        );
    }

    public function getLangsAvailable() {
        $langs = [];
        $sql = "
                SELECT lang
                FROM`communication`
                WHERE id = :id
                UNION DISTINCT
                SELECT lang
                FROM `communication_lang`
                WHERE id = :id
              ";
        try {
            $query = static::query($sql, array(':id' => $this->id));
            while ($lang = $query->fetchColumn()) {
                array_push($langs, $lang);
            }
        } catch (\Exception $e) {
        }
        return $langs;
    }

    public function getAllLangs() {
        try {
            $sql = "SELECT a.id, a.lang, a.subject, a.content FROM `{$this->Table}` a WHERE a.id = :id
                    UNION
                    SELECT * FROM `{$this->Table}_lang` b WHERE b.id = :id";
            $values = array(':id' => $this->id);
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                return $query->fetchAll(\PDO::FETCH_OBJ);
            }
        } catch (\Exception $e) {}
        return [];
    }

    public function getStatus() {
        $mails = Mail::getFromCommunicationId($this->id);
        $success = 0;
        if ($mails) {
            foreach($mails as $mail) {
                $success += round($mail->getStats()->getEmailOpenedCollector()->getPercent());
            }
            return round($success/sizeof($mails));
        }

        return $success;
    }

    public function isActive() {
        $mails = Mail::getFromCommunicationId($this->id);
        $active = 0;

        if ($mails) {
            foreach($mails as $mail) {
                if ($active)
                    break;
                if ($mail->getSender())
                    $active = $active || $mail->getSender()->isActive();
            }
        }

        return $active;
    }

    public function isSent() {
        $mails = Mail::getFromCommunicationId($this->id);
        $sent = true;

        if ($mails) {
            foreach($mails as $mail) {
                if ($mail->getSender())
                    if (!$mail->getSender()->getStatusObject()->sent)
                        return false;
            }
        }

        return $sent;

    }

}

