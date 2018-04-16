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
use Goteo\Model\Mail;
use Goteo\Model\Project;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;

class Message extends \Goteo\Core\Model {

    public
        $id,
        $user,
        $project,
        $thread, // hilo al que contesta, si es NULL es un hilo y tendrá respuestas ( o no)
        $date, // timestamp del momento en que se creó el mensaje
        $subject, // if set, used as subject instead of template default subject (if this message is sent by mail)
        $message, // el texto del mensaje en si
        $responses = array(), // array de instancias mensaje que son respuesta a este
        $all_responses = [], // cache array
        $blocked = 0, //no se puede editar ni borrar (es un mensaje thread de colaboracion)
        $closed = 0, // no se puede responder
        $private = 0, // private messages uses table 'message_users' for searching recipients
        $recipients = [], // Recipients if is a private message
        $participants = [], // All participants from a message (includes private recipients)
        $timeago;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->timeago = Feed::time_ago($this->date);
        // $this->message = nl2br(Text::urlink($this->message));
    }

    static public function getLangFields() {
        return ['message'];
    }

    /*
     *  Devuelve datos de un mensaje
     */
    public static function get ($id, $lang = null) {


        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="
            SELECT  message.id,
                    message.user,
                    message.project,
                    message.thread,
                    message.date,
                    $fields,
                    message.blocked,
                    message.closed,
                    message.private,
                    user.id as user_id,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar
            FROM    message
            $joins
            INNER JOIN user ON user.id=message.user
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
            // $message->message = nl2br(Text::urlink($message->message));

            // //hace tanto
            // $message->timeago = Feed::time_ago($message->date);

            // Deprecated: to be removed
            if (empty($message->thread)) {
                $query = self::query("
                    SELECT  *
                    FROM  message
                    WHERE
                        private = false
                        AND
                        thread = ?
                    ", array($id));

                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $response) {
                    $message->responses[] = $response;
                }

            }
        }
        return $message;
    }

    /*
     * Lista de hilos de un proyecto
     */
    public static function getAll ($project, $lang = null, $with_private = false) {
        if($project instanceOf Project) $project = $project->id;

        $messages = array();

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="
              SELECT
                message.id as id,
                message.user as user,
                message.project as project,
                message.thread as thread,
                message.date as date,
                $fields,
                message.blocked as blocked,
                message.closed as closed,
                message.private as private,
                user.id as user_id,
                user.name as user_name,
                user.email as user_email,
                user.avatar as user_avatar
            FROM  message
            INNER JOIN user ON user.id=message.user
            $joins
            WHERE   message.project = :project
            AND     message.thread IS NULL
            " . ($with_private ? '' : ' AND private=0 ') . "
            ORDER BY date ASC, id ASC
            ";
        $query = static::query($sql, array(':project' => $project));
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

    /**
     * Returns user messages
     */
    public static function getUserThreads($user, $filters = [], $offset = 0, $limit = 10, $count = false, $order = 'date_max DESC, date DESC, id DESC') {

        $id = $user instanceOf User ? $user->id : $user;
        $values = [':user' => $id];
        // $sql = 'FROM message a
        //           LEFT JOIN message_user d ON d.message_id=a.id
        //           WHERE (
        //             a.id IN (
        //                 SELECT thread FROM message b
        //                 WHERE (b.user = :user OR
        //                     :user IN (SELECT user_id FROM message_user c WHERE c.message_id=b.id)
        //                     ) AND b.blocked=0)
        //             OR d.user_id = :user)
        //             AND ISNULL(a.thread)
        //             ';
        $sqlFilter = [];
        if($filters['project']) {
            $parts = [];
            if(!is_array($filters['project'])) $filters['project'] = [$filters['project']];
            foreach(array_values($filters['project']) as $i => $prj) {
                $parts[] = ':prj' . $i;
                $values[':prj' . $i] = is_object($prj) ? $prj->id : $prj;
            }
            $sqlFilter[] = "a.project IN (" . implode(',', $parts) . ")";
        }
        if($filters['recipient']) {
            $parts = [];
            if(!is_array($filters['recipient'])) $filters['recipient'] = [$filters['recipient']];
            foreach(array_values($filters['recipient']) as $i => $rcp) {
                $parts[] = ':rcp' . $i;
                $values[':rcp' . $i] = is_object($rcp) ? $rcp->id : $rcp;
            }
            $sqlFilter[] = "b.user_id IN (" . implode(',', $parts) . ")";
        }

        if($sqlFilter) {
            $sqlFilter = ' AND (' . implode(' AND ', $sqlFilter) . ')';
        } else {
            $sqlFilter = '';
        }
        $sql = "FROM message a
                LEFT JOIN message_user b ON b.message_id=a.id
                WHERE (a.user = :user OR b.user_id=:user) $sqlFilter
                GROUP BY IF(ISNULL(a.thread),a.id,a.thread)
                HAVING ISNULL(a.thread) AND a.blocked=0
            ";

        if($count) {
            // return (int) self::query("SELECT COUNT(DISTINCT a.id) $sql", $values)->fetchColumn();
            return (int) self::query("SELECT COUNT(*) FROM (SELECT a.thread,a.blocked $sql) s", $values)->fetchColumn();
        }

        // $sql = "SELECT DISTINCT a.* $sql";
        $sql = "SELECT a.*, MAX(a.date) AS date_max $sql";
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql .=  $order ? " ORDER BY $order" : '';
        $sql .= " LIMIT $offset, $limit";

        // if($sqlFilter) die(\sqldbg($sql, $values));
        $query = self::query($sql, $values);
        if($resp = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__)) {
            return $resp;
        }
        return [];

    }

    /**
     * Returns user messages
     */
    public static function getUserMessages($user, $project = null, $offset = 0, $limit = 10, $count = false) {
        $id = $user instanceOf User ? $user->id : $user;
        $pid = $project instanceOf Project ? $project->id : $project;

        $addWhere = '';
        $values = [':user' => $id];
        if($project) {
            $addWhere .= ' AND a.project = :project';
            $values[':project'] = $pid;
        }
        $sql = "FROM message a
                JOIN `user` u1 ON a.user = u1.id
                LEFT JOIN support b ON b.thread = a.thread
                LEFT JOIN message_user c ON c.message_id = a.id
                LEFT JOIN `user` u2 ON c.user_id = u2.id
                WHERE blocked=0
                AND (c.user_id = :user OR a.user = :user)
                AND b.id IS NULL
                $addWhere";
        if($count) {
            return (int) self::query("SELECT COUNT(a.id) $sql", $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT a.*, b.id AS support_id,
        IF(ISNULL(u2.id), u1.id, u2.id) AS recipient,
        IF(ISNULL(u2.name), u1.name, u2.name)  AS recipient_name $sql ORDER BY date DESC, id DESC LIMIT $offset, $limit";

        $sql = "SELECT * FROM ($sql) rev ORDER BY date ASC, id ASC ";
        // die(sqldbg($sql, $values));
        $query = self::query($sql, $values);
        if($resp = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__)) {
            return $resp;
        }
        return [];

    }

    /**
     * Assigns thread
     * In mode 'auto' search for the most recent parent thread with
     * the same caracteristics: same user in the replies and same project
     * @param [type] $thread [description]
     */
    public function setThread($thread) {
        if($thread === 'auto') {
            if($lasts = static::getUserThreads($this->user, ['project' => $this->project, 'recipient' => $this->getRecipients()], 0, 2)) {
                // Make sure is not the same message
                foreach($lasts as $last) {
                    if($last->id != $this->id)
                        $this->thread = $last->id;
                }
            }
        } else {
            $this->thread = $thread;
        }
        return $this;
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

    public function getProject() {
        if(!$this->project) return null;
        if($this->projectInstance instanceOf Project) return $this->projectInstance;
        $this->projectInstance = Project::get($this->project);
        return $this->projectInstance;
    }

    public function getMail() {
        if($this->emailInstance) return $this->emailInstance;
        $this->emailInstance = Mail::getFromMessageId($this->id);
        return $this->emailInstance;
    }

    public function getStats() {
        if($mail = $this->getMail()) return $mail->getStats();
        return null;
    }

    // Description title from project
    public function getTitle() {
        if($this->project) return $this->getProject()->name;
    }

    // Get the message content parsed as html
    public function getHtml() {
        return App::getService('app.md.parser')->text($this->message);
    }

    public function getSubject() {
        if($this->subject) return $this->subject;
        return trim(str_replace('### ', '', strtok($this->message, "\n")));
    }

    /**
     * response
     * project-comment (project comment non related to support)
     * project-comment-response (response for project comment non related to support)
     * project-support (mirror message from support)
     * project-support-response (support message response)
     * project-private (for donors communication)
     * project-private-response (responses from donors communication)
     * @return string
     */
    public function getType() {
        $type = '';
        if($this->project) {
            if($this->private) {
                $type = 'project-private';
            } else {
                $type = 'project-comment';
            }
            $sql = "SELECT id FROM support WHERE thread = :id";
            $values = [':id' => $this->id];
            if($this->thread) {
                $values[':id'] = $this->thread;
            }
            $query = self::query($sql, $values);
            if($query->fetchColumn()) {
                $type = 'project-support';
            }
            if($this->thread) {
                $type .= '-response';
            }
        } else {
            if($this->thread) {
                $type = 'response';
            }
            // TODO: more types...
        }
        return $type;
    }

    /**
     * return all user responses
    */
    public function getResponsesStatic($thread, $user = null, $offset = 0, $limit = 10, $count = false) {
        $user_id = $user instanceOf User ? $user->id : null;

        $sql = "LEFT JOIN message_user b ON b.message_id = a.id AND b.user_id=:user
                WHERE a.thread = :thread";

        $values = [':user' => $user_id, ':thread' => $thread];
        if($count) {
            return (int) self::query("SELECT COUNT(a.id) FROM message a $sql", $values)->fetchColumn();
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT a.* FROM message a $sql ORDER BY date DESC, id DESC LIMIT $offset, $limit";
        $sql = "SELECT * FROM ($sql) rev ORDER BY date ASC, id ASC ";
        // echo \sqldbg($sql, $values);
        $query = self::query($sql, $values);
        if($resp = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__)) {
            return $resp;
        }
        return [];
    }

    public function getResponses($user = null, $offset = 0, $limit = 10, $count = false, $order = 'date ASC, id ASC') {
        return static::getResponsesStatic($this->id, $user, $offset, $limit, $count, $order);
    }

    public function totalResponses(User $user = null) {
        $user_id = '';
        if($user) $user_id = $user->id;
        if($this->total_thread_responses[$user_id]) return $this->total_thread_responses[$user_id];
        if($this->id) {
            $this->total_thread_responses[$user_id] = $this->getResponses($user, 0, 0, true);
            return $this->total_thread_responses[$user_id];
        }
        return 0;
    }

    public function setRecipients(array $recipients = []) {
        if($recipients) {
            $this->private = true;
            $this->save();
            $values = [':message' => $this->id];
            $i = 0;
            foreach($recipients as $user) {
                if($user instanceOf User) $user = $user->id;
                $user = trim($user);
                if($user) {
                    $sql = "INSERT INTO message_user (message_id, user_id) VALUES(:message, :user)";
                    self::query($sql, [':message' => $this->id ,':user' => $user]);
                    $values[":user$i"] = $user;
                    $i++;
                }
            }
            $sql = 'DELETE FROM message_user WHERE message_id = :message AND user_id NOT IN (' . implode(',', array_keys($values)) . ')';
            self::query($sql, $values);
        }

    }

    // User in private messages
    public function getRecipients() {
        if(!$this->recipients) {
            $sql = "SELECT user.* FROM `user`
                RIGHT JOIN message_user ON message_user.user_id = user.id
                WHERE message_user.message_id = :id";

            $query = self::query($sql, [':id' => $this->id]);
            if($resp = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User')) {
                foreach($resp as $user) {
                    $this->recipients[$user->id] = $user;
                }
            }
        }
        return $this->recipients;
    }

    // Users on the same thread
    public function getParticipants() {
        if(!$this->participants) {
            $sql = "SELECT DISTINCT user.* FROM `user`
                RIGHT JOIN message a ON a.user = user.id
                WHERE a.thread IN ( SELECT id FROM message b WHERE b.id = :id )";

            $query = self::query($sql, [':id' => $this->id]);
            $this->participants = $this->getRecipients();
            if($resp = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User')) {
                foreach($resp as $user) {
                    $this->participants[$user->id] = $user;
                }
            }
        }
        return $this->participants;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;
        if ($this->user instanceOf User) {
            $this->user = $this->user->id;
        }

        // HTML tags cleaning
        $this->message = Text::tags_filter($this->message);

        $fields = array(
            'id',
            'user',
            'project',
            'thread',
            'message',
            'blocked',
            'closed',
            'private'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            // actualizar campo calculado
            self::numMessengers($this->project);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Support save error: " . $e->getMessage();
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
        try {
            if( ! $project instanceOf Project ) $project = Project::getMini($project);
        } catch(ModelNotFoundException $e) {
            return 0;
        }
        $values = array(':project' => $project->id, ':user' => $project->getOwner()->id);

        $sql = "SELECT  COUNT(*) as messengers, project.num_messengers as num, project.num_investors as pop
            FROM    message
            INNER JOIN project
                ON project.id = message.project
            WHERE   message.project = :project
            AND message.user != :user
            AND message.private = 0
            ";

        // die(\sqldbg($sql, $values));

        $query = static::query($sql, $values);
        if($got = $query->fetchObject()) {
            // si ha cambiado, actualiza el numero de colaboraciones en proyecto
            if ($got->messengers != $got->num) {
                static::query("UPDATE project SET num_messengers = :num, popularity = :pop WHERE id = :project", array(':num' => (int) $got->messengers, 'pop' => ( $got->messengers + $got->pop), ':project' => $project->id));
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

