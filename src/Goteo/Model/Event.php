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
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\DuplicatedEventException;

/**
 * Event Model
 */
class Event extends \Goteo\Core\Model {
    public $id,
           $type = 'communication',
           $action,
           $hash,
           $created,
           $finalized,
           $succeeded = false,
           $error,
           $result;

    public function __construct($action, $type = 'communication') {
        // Parent constructor without arguments
        parent::__construct();
        $this->setAction($action);
        $this->type = $type;
    }

    /**
     * Get instance of event already in the table by action
     * @return [type] [description]
     */
    static public function get($action) {
        $hash = md5(self::encodeAction($action));
        return self::getByHash($hash, 'hash');
    }

    /**
     * Get instance of event already in the table by Id
     * @return [type] [description]
     */
    static public function getById($id) {
        return self::getFrom($id, 'id');
    }

    /**
     * Get instance of event already in the table by hash
     * @return [type] [description]
     */
    static public function getByHash($hash) {
        return self::getFrom($hash, 'hash');
    }

    /**
     * Get instance of event already in the table by Id
     * @return [type] [description]
     */
    static public function getFrom($id, $field = 'hash') {
        if ($query = static::query("SELECT * FROM event WHERE `$field` = ?", $id)) {
            if( $event = $query->fetchObject(__CLASS__) )
                return $event;
        }
        throw new ModelNotFoundException("Event [$id] not found");
    }

    /**
     * Checks if the current event already exists
     * @param boolean $succeeded if truKe, only checks for succeeded events
     * @return [type] [description]
     */
    static public function exists($hash, $succeeded = true) {
        $values = [':hash' => $hash];
        $sql = "SELECT COUNT(*) FROM event WHERE `hash` = :hash";
        if($succeeded) {
            $sql .= " AND succeeded = :succeeded";
            $values[':succeeded'] = true;
        }
        if ($query = static::query($sql, $values)) {
            return $query->fetchColumn() >= 1;
        }
        return false;
    }

    static public function encodeAction($action) {
        return is_array($action) ? implode(':', $action) : $action;
    }

    /**
     * Saves the event to the table with the specified action
     * If the action exists as a previous succeeded one, Exception will be thrown
     * @param misc $action action
     * @thrown ModelException
     */
    public function setAction($action) {
        $this->action = self::encodeAction($action);
        $this->hash = md5($this->action);
        // check non-repeated
        if(self::exists($this->hash)) {
            throw new DuplicatedEventException($this->action);
        }
    }


    /**
     * Guardar.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if(!$this->validate($errors)) return false;

        $this->created = date('Y-m-d H:i:s');
        // $this->created = date('Y-m-d H:i:s');
        try {
            $this->dbInsertUpdate(['type', 'action', 'hash', 'created', 'finalized', 'succeeded', 'desc', 'error']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving event: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validar. check if event is not-duplicated
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        if(self::exists($this->hash)) {
            $errors[] = 'Duplicated succeeded event';
        }
        return empty($errors);
    }

    /**
     * Sets the PHP callback associated with this Response.
     *
     * @param callable $callback A valid PHP callback
     *
     * @throws \LogicException
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \LogicException('The Response callback must be a valid PHP callable.');
        }
        $this->callback = $callback;
    }

    /**
     * Executes the callback and saves the result to the database
     *
     * @param callable $callback A valid PHP callback
     *
     * @throws \LogicException, Goteo\Model\Exception\ModelException
     */
    public function fire($callback = null) {

        if($callback) $this->setCallback($callback);
        $errors = [];
        if(!$this->save($errors)) {
            throw new ModelException("Error saving callback result: " . implode("\n", $errors));
        }

        try {
            $result = call_user_func($this->callback);
            // No Exceptions
            $this->succeeded = true;
            $this->result = $result;
        } catch(\Exception $e) {
            $this->succeeded = false;
            $this->error = $e->getMessage();
            $result = 'Exception';
        }
        $this->finalized = date('Y-m-d H:i:s');

        if(!$this->save($errors)) {
            throw new ModelException("Error saving callback result: " . implode("\n", $errors));
        }

        return $result;

    }
}
