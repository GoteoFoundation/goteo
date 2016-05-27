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

use Goteo\Core\Model;
use Goteo\Model\Project;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\DuplicatedEventException;

class Favourite extends \Goteo\Core\Model {

    protected $Table = 'user_favourite_project';

    public
        $project,
        $user,
        $date_send,
        $date_marked;

    /*
     *  Get list of users project favourite
     */
    public static function get ($project) {

            $query = static::query("
                SELECT *
                    FROM user_favourite_project
                WHERE project = :project
                ", array(':project' => $project));

            $favourite = $query->fetchObject('\Goteo\Model\Project\Favourite');

            return $favourite;
    }

     /*
     *  Return if a project is favourite
     */
    public static function isFavouriteProject ($project, $user) {

            $query = static::query("
                SELECT *
                    FROM user_favourite_project
                WHERE project = :project AND user = :user 
                ", array(':project' => $project, ':user' => $user));

            if ($row = $query->fetch())
                return true;
            else
                return false;

    }

    /*
     *  Return a list of users where the project is favourite and have to be sent today
     */
    public static function usersSentToday ($project) {



            $today=DATE('Y-m-d');

            $query = static::query("
                SELECT user.id as user_id,
                       user.name as user_name,
                       user.email as user_email,
                       user.lang as user_lang
                    FROM user_favourite_project
                    LEFT JOIN user
                    ON user.id=user_favourite_project.user
                WHERE project = :project AND date_send = :today 
                ", array(':project' => $project, ':today' => $today));

        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
            $list[] = $item;
        }

        return $list;

            if ($row = $query->fetch())
                return true;
            else
                return false;

    }

   
    public function validate (&$errors = array()) {
        if (empty($this->project))
            $errors[] = 'Falta proyecto'; 

        if (empty($this->user))
            $errors[] = 'Falta user';

        if (empty($errors))
            return true;
        else
            return false;
    }


    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        $this->date_marked=date('Y-m-d');

        $fields = array(
            'project',
            'user',
            'date_send',
            'date_marked'
        );





        try {
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Favourite save error: " . $e->getMessage();
            return false;
        }
    }

    /**
         * Remove a project as favourite of a user
         *
         * @param varchar(50) $project project id
         * @param varchar(50) $user  user id
         * @param array $errors
         * @return boolean
         */
        public function remove (&$errors = array()) {
            $values = array (
                ':project'=>$this->project,
                ':user'=>$this->user,
            );

            try {
                self::query("DELETE FROM user_favourite_project WHERE user = :user AND project = :project", $values);
                return true;
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido borrar el proyecto favorito' . $e->getMessage();
                return false;
            }
        }


}
