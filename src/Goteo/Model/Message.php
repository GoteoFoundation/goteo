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

use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Model\User;
use Goteo\Model\Image;
use Goteo\Model\Project;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class Message extends \Goteo\Core\Model {

    public
        $id,
        $user,
        $project,
        $thread, // hilo al que contesta, si es NULL es un hilo y tendrá respuestas ( o no)
        $date, // timestamp del momento en que se creó el mensaje
        $message, // el texto del mensaje en si
        $responses = array(), // array de instancias mensaje que son respuesta a este
        $blocked = 0, //no se puede editar ni borrar (es un mensaje thread de colaboracion)
        $closed = 0, // no se puede responder
        $timeago;

    /*
     *  Devuelve datos de un mensaje
     */
    public static function get ($id) {

            $sql="
                SELECT  message.*,
                        user.id as user_id,
                        user.name as user_name,
                        user.email as user_email,
                        user.avatar as user_avatar
                FROM    message
                INNER JOIN user
                ON user.id=message.user
                WHERE   message.id = :id
                ";

            $query = self::query($sql, array(':id' => $id));
            if($message = $query->fetchObject(__CLASS__)) {

                // datos del usuario. Eliminación de user::getMini

                $user = new User;
                $user->id = $message->user_id;
                $user->name = $message->user_name;
                $user->email = $message->user_email;
                $user->avatar = Image::get($message->user_avatar);

                $message->user = $user;


                // reconocimiento de enlaces y saltos de linea
                $message->message = nl2br(Text::urlink($message->message));

                //hace tanto
                $message->timeago = Feed::time_ago($message->date);

                if (empty($message->thread)) {
                    $query = self::query("
                        SELECT  *
                        FROM  message
                        WHERE thread = ?
                        ", array($id));

                    foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $response) {

                        //hace tanto
                        $response->timeago = Feed::time_ago($response->date);

                        $message->responses[] = $response;
                    }

                }
            }
            return $message;
    }

    /*
     * Lista de hilos de un proyecto
     */
    public static function getAll ($project, $lang = null) {
        if($project instanceOf Project) $project = $project->id;

        if(empty($lang)) $lang = Lang::current();
        $messages = array();

        if(self::default_lang($lang) === Config::get('lang')) {
            $different_select=" IFNULL(message_lang.message, message.message) as message";
            }
        else {
                $different_select=" IFNULL(message_lang.message, IFNULL(eng.message, message.message)) as message";
                $eng_join=" LEFT JOIN message_lang as eng
                                ON  eng.id = message.id
                                AND eng.lang = 'en'";
            }

        $sql="
              SELECT
                message.id as id,
                message.user as user,
                message.project as project,
                message.thread as thread,
                message.date as date,
                $different_select,
                message.blocked as blocked,
                message.closed as closed,
                user.id as user_id,
                user.name as user_name,
                user.email as user_email,
                user.avatar as user_avatar
            FROM  message
            INNER JOIN user
            ON user.id=message.user
            LEFT JOIN message_lang
                ON  message_lang.id = message.id
                AND message_lang.lang = :lang
            $eng_join
            WHERE   message.project = :project
            AND     message.thread IS NULL
            ORDER BY date ASC, id ASC
            ";
        $query = static::query($sql, array(':project'=>$project, ':lang'=>$lang));
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $message) {

            // datos del usuario. Eliminación User::getMini

            $user = new User;
            $user->id = $message->user_id;
            $user->name = $message->user_name;
            $user->email = $message->user_email;
            $user->avatar = Image::get($message->user_avatar);

            $message->user = $user;

            // reconocimiento de enlaces y saltos de linea
            $message->message = nl2br(Text::urlink($message->message));

            //hace tanto
            $message->timeago = Feed::time_ago($message->date);

            $query = static::query("
                SELECT  id
                FROM  message
                WHERE thread = ?
                ORDER BY date ASC, id ASC
                ", array($message->id));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $response) {
                $message->responses[$response->id] = self::get($response->id);
            }




            $messages[$message->id] = $message;
        }

        return $messages;
    }


    public function validate (&$errors = array()) {
        if (empty($this->user))
            $errors[] = 'Falta usuario';
            //Text::get('mandatory-message-user');

        if (empty($this->project))
            $errors[] = 'Falta proyecto';
            //Text::get('validate-message-noproject');

        if (empty($this->message))
            $errors[] = 'Falta texto';
            //Text::get('mandatory-message-text');

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function getUser() {
        if($this->user instanceOf User) return $this->user;
        $this->user = User::get($this->user);
        return $this->user;
    }
    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        if ($this->user instanceOf User) {
            $this->user = $this->user->id;
        }
        else {
            //TODO: por coherencia hacer algo asi:
            // $errors[] = 'User must be defined!';
            // return false;
        }

        $fields = array(
            'id',
            'user',
            'project',
            'thread',
            'message',
            'blocked',
            'closed'
            );

        $set = '';
        $values = array();

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }
        }

        //eliminamos etiquetas script,iframe..
        $values[':message']=Text::tags_filter($values[':message']);

        try {
            $sql = "REPLACE INTO message SET " . $set;
            self::query($sql, $values);
            if (empty($this->id)) $this->id = self::insertId();

            // actualizar campo calculado
            self::numMessengers($this->project);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "El mensaje no se ha grabado correctamente. Por favor, inténtelo de nuevo." . $e->getMessage();
            return false;
        }
    }

    public function saveLang (&$errors = array()) {
        $fields = array(
            'id'=>'id',
            'lang'=>'lang',
            'message'=>'message_lang'
            );

        $set = '';
        $values = array();

        foreach ($fields as $field=>$ffield) {
            if (!empty($this->$ffield)) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$ffield;
            }
        }

        //eliminamos etiquetas script,iframe..
        $values[':message']=Text::tags_filter($values[':message']);

        try {
            $sql = "REPLACE INTO message_lang SET " . $set;
            self::query($sql, $values);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "El mensaje no se ha grabado correctamente. Por favor, inténtelo de nuevo." . $e->getMessage();
            return false;
        }
    }

    /*
     * Para que el admin pueda borrar mensajes que no aporten nada
     */
    public static function delete ($id, &$errors = array()) {

        if(empty($id)) {
            // throw new Exception("Delete error: ID not defined!");
            return false;
        }
        $m = self::get($id);

        try {

            if ($m->blocked == 1) {
                return false;
            }

            $sql = "DELETE FROM message WHERE id = ?";
            self::query($sql, array($id));

            if (empty($m->thread) && is_array($m->responses)) {
                foreach ($m->responses as $response) {
                    if ($response instanceof Message) {
                        $response->dbDelete();
                    }
                }
            }

        } catch (\PDOException $e) {
            // throw new Exception("Delete error in $sql");
            return false;
        }
        return true;

    }


    /**
     * Para saber si un hilo de mensajes es de colaboración
     *
     * @param numeric $id Id del mensaje (thread)
     * @return bool true/false
     */
    public static function isSupport ($id) {
        $sql = "SELECT support FROM support WHERE thread = :id";
        $query = self::query($sql, array(':id'=>$id));
        $support = $query->fetchColumn();

        if (empty($support)) {
            return false;
        } else {
            return $support;
        }
    }

    /*
     * Numero de usuarios mensajeros de un proyecto
     */
    public static function numMessengers ($project) {

        $debug = false;

        $values = array(':project' => $project);

        $sql = "SELECT  COUNT(*) as messengers, project.num_messengers as num, project.num_investors as pop
            FROM    message
            INNER JOIN project
                ON project.id = message.project
            WHERE   message.project = :project
            ";

        if ($debug) {
            echo \trace($values);
            echo $sql;
            die;
        }

        $query = static::query($sql, $values);
        if($got = $query->fetchObject()) {
            // si ha cambiado, actualiza el numero de inversores en proyecto
            if ($got->messengers != $got->num) {
                static::query("UPDATE project SET num_messengers = :num, popularity = :pop WHERE id = :project", array(':num' => (int) $got->messengers, 'pop' => ( $got->messengers + $got->pop), ':project' => $project));
            }
        }

        return (int) $got->messengers;
    }


    /*
     * Lista de usuarios mensajeros de un proyecto
     */
    public static function getMessengers ($id) {
        $list = array();

        $sql = "SELECT
                    message.user as user,
                    message.message as text,
                    respond.message as thread_text,
                    user.id as user_id,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar

                FROM message
                LEFT JOIN message as respond
                    ON respond.id = message.thread
                INNER JOIN user
                    ON user.id=message.user
                WHERE message.project = :id";
        $query = self::query($sql, array(':id'=>$id));
        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $msg) {

            $msgData = (object) array(
                    'text' => $msg->text,
                    'thread_text' => $msg->thread_text
                );

            if (isset($list[$msg->user])) {
                $list[$msg->user]->messages[] = $msgData;
            } else {
                //Eliminación User::getMini

                $user = new User;
                $user->id = $msg->user_id;
                $user->name = $msg->user_name;
                $user->email = $msg->user_email;
                $user->avatar = Image::get($msg->user_avatar);

                $user->messages = array();
                $user->messages[] = $msgData;
                $list[$msg->user] = $user;
            }
        }

        return $list;
    }

    /*
     * Lista de proyectos mensajeados por un usuario (proyectos en los que el usuario es participante)
     */
    public static function getMesseged($user, $publicOnly = true)
    {
        $projects = array();

        $sql = "SELECT project.id
                FROM  project
                INNER JOIN message
                    ON project.id = message.project
                    AND message.user = ?
                WHERE project.status < 7
                ";
        if ($publicOnly) {
            $sql .= "AND project.status >= 3
                ";
        }
        $sql .= "GROUP BY project.id
                ORDER BY name ASC
                ";

        $query = self::query($sql, array($user));
        foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $proj) {
            $projects[] = \Goteo\Model\Project::getMedium($proj->id);
        }
        return $projects;
    }

}

