<?php

namespace Goteo\Model {

    use Goteo\Library\Text;

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
            $closed = 0; // no se puede responder

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
                $message->user = User::get($message->user);

                // reconocimiento de enlaces y saltos de linea
                $message->message = nl2br(Text::urlink($message->message));

                if (empty($message->thread)) {
                    $query = static::query("
                        SELECT  *
                        FROM  message
                        WHERE thread = ?
                        ", array($id));
                    $message->responses = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
                }

                return $message;
        }

        /*
         * Lista de hilos de un proyecto
         */
        public static function getAll ($project) {

            $messages = array();

            $query = static::query("
                SELECT  *
                FROM  message
                WHERE   message.project = ?
                AND     message.thread IS NULL
                ORDER BY date ASC, id ASC
                ", array($project));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $message) {
                // datos del usuario
                $message->user = User::get($message->user);
                
                // reconocimiento de enlaces y saltos de linea
                $message->message = nl2br(Text::urlink($message->message));

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

    }
    
}