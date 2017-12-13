<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\Project;

use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\Model\Message as Comment;

class Support extends \Goteo\Core\Model {

    public
        $id,
		$project,
		$support,
		$description,
		$type = 'task',
        $total_thread_responses = [],
        $thread;

    public static function getLangFields() {
        return ['support', 'description'];
    }

    public function setLang($lang, $data = [], array &$errors = []) {
        $data['project'] = $this->project;
        return parent::setLang($lang, $data, $errors);
    }

 	public static function get ($id) {
        try {
            $query = static::query("SELECT * FROM support WHERE id = :id", array(':id' => $id));
            return $query->fetchObject(__CLASS__);
        } catch(\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
	}

	public static function getAll ($project, $lang = null) {
        try {
            $array = array();

            if($project instanceOf Project) {
                $values = array(':project' => $project->id);
                list($fields, $joins) = self::getLangsSQLJoins($lang, $project->lang);
            }
            else {
                $values = array(':project' => $project);
                list($fields, $joins) = self::getLangsSQLJoins($lang, 'project', 'project');
            }

            $sql = "SELECT
                        support.id as id,
                        support.project as project,
                        support.type as type,
                        $fields,
                        support.thread as thread
                    FROM support
                    $joins
                    WHERE support.project = :project
                    ORDER BY support.id ASC
                    ";

			$query = self::query($sql, $values);
			foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item ) {
                $array[$item->id] = $item;
            }
            // print_r($array);die;
			return $array;
        } catch(\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
	}

	public function validate(&$errors = array()) {
        // Estos son errores que no permiten continuar
        if (empty($this->project))
            $errors[] = 'No hay proyecto al que asignar la colaboración';
            //Text::get('validate-collaboration-noproject');

        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

	public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

		$fields = array(
			// 'id',
			'project',
			'support',
			'type',
			'description',
            'thread'
			);

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Support save error: " . $e->getMessage();
            return false;
        }
	}

	public function saveLang (&$errors = array()) {
		$fields = array(
			'id'=>'id',
            'project'=>'project',
			'lang'=>'lang',
			'support'=>'support_lang',
			'description'=>'description_lang'
			);

		$set = '';
		$values = array();

		foreach ($fields as $field=>$ffield) {
			if ($set != '') $set .= ", ";
			$set .= "$field = :$field ";
			$values[":$field"] = $this->$ffield;
		}

		try {
			$sql = "REPLACE INTO support_lang SET " . $set;
			self::query($sql, $values);

			return true;
		} catch(\PDOException $e) {
			$errors[] = "La colaboración {$data['support']} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
		}
	}

    /** Retrieve related messages to this support entry */
    public function getThread() {
        if($this->threadInstance instanceOf Comment) return $this->threadInstance;
        if($this->thread) {
            $this->threadInstance = Comment::get($this->thread);
            return $this->threadInstance;
        }
        return null;
    }

    /* returns responses to this support from the message table */
    public function getThreadResponses(User $user = null) {
        if($thread = $this->getThread()) {
            return $thread->getResponses($user);
        }
        return [];
    }

    /* returns number of responses to this support from the message table */
    public function totalThreadResponses(User $user = null) {
        if($thread = $this->getThread()) {
            return $thread->totalResponses($user);
        }
        return 0;
    }

	/**
	 * Quitar una colaboracion de un proyecto
	 *
	 * @param varchar(50) $project id de un proyecto
	 * @param INT(12) $id  identificador de la tabla support
	 * @param array $errors
	 * @return boolean
	 */
	public function remove (&$errors = array()) {
		$values = array (
			':project'=>$this->project,
			':id'=>$this->id,
		);

        try {
            self::query("DELETE FROM support WHERE id = :id AND project = :project", $values);

            //quitar el mensaje
            //It may be removed, using CASCADE from MySQL instead
            self::query("DELETE FROM message WHERE id = ?", array($this->thread));


			return true;
		} catch (\PDOException $e) {
            $errors[] = 'No se ha podido quitar la colaboracion del proyecto ' . $this->project . ' ' . $e->getMessage();
            //Text::get('remove-collaboration-fail');
            return false;
		}
	}

	public static function types() {
		return array(
			'task'=>Text::get('cost-type-task'),
			'lend'=>Text::get('cost-type-lend')
		);

	}

}

