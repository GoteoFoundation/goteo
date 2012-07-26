<?php

namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Library\Feed;

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
                $query = static::query("
                    SELECT  *
                    FROM    message
                    WHERE   id = :id
                    ", array(':id' => $id));
                $message = $query->fetchObject(__CLASS__);
                
                // datos del usuario
                $message->user = User::getMini($message->user);

                // reconocimiento de enlaces y saltos de linea
                $message->message = nl2br(Text::urlink($message->message));

                //hace tanto
                $message->timeago = Feed::time_ago($message->date);

                if (empty($message->thread)) {
                    $query = static::query("
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

                return $message;
        }

        /*
         * Lista de hilos de un proyecto
         */
        public static function getAll ($project, $lang = null) {

            $messages = array();

            $query = static::query("
                SELECT
                    message.id as id,
                    message.user as user,
                    message.project as project,
                    message.thread as thread,
                    message.date as date,
                    IFNULL(message_lang.message, message.message) as message,
                    message.blocked as blocked,
                    message.closed as closed
                FROM  message
                LEFT JOIN message_lang
                    ON  message_lang.id = message.id
                    AND message_lang.lang = :lang
                WHERE   message.project = :project
                AND     message.thread IS NULL
                ORDER BY date ASC, id ASC
                ", array(':project'=>$project, ':lang'=>$lang));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $message) {
                // datos del usuario
                $message->user = User::getMini($message->user);
                
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
                    $message->responses[] = self::get($response->id);
                }
                



                $messages[] = $message;
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

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            if (\is_object($this->user)) {
                $this->user = $this->user->id;
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

            try {
                $sql = "REPLACE INTO message SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

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
        public function delete () {

            if ($this->blocked == 1) {
                return false;
            }

            $sql = "DELETE FROM message WHERE id = ?";
            if (self::query($sql, array($this->id))) {
                if (empty($this->thread) && is_array($this->responses)) {
                    foreach ($this->responses as $response) {
                        if ($response instanceof Message) {
                            $response->delete();
                        }
                    }
                }
                return true;
            } else {
                return false;
            }

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
        public static function numMessegers ($id) {
            $sql = "SELECT COUNT(DISTINCT(message.user)) FROM message WHERE project = :id";
            $query = self::query($sql, array(':id'=>$id));
            $num = $query->fetchColumn();

            if (empty($num)) {
                return false;
            } else {
                return $num;
            }
        }

        /*
         * Lista de usuarios mensajeros de un proyecto
         */
        public static function getMessegers ($id) {
            $list = array();

            $sql = "SELECT 
                        message.user as user,
                        message.message as text,
                        respond.message as thread_text
                    FROM message
                    LEFT JOIN message as respond
                        ON respond.id = message.thread
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
                    $user = User::getMini($msg->user);
                    $user->messages = array();
                    $user->messages[] = $msgData;
                    $list[$msg->user] = $user;
                }
            }

            return $list;
        }

    }
    
}