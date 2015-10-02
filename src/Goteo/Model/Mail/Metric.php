<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Mail;

use Goteo\Application\Exception\ModelException;

class Metric extends \Goteo\Core\Model
{
    protected $Table = 'metric';
    public
        $id,
        $metric, // UNIQUE KEY
        $desc;

    /**
     * Returns a metric, cretes new object if not exists
     * @param  string $metric Metric string
     * @return Metric     object
     */
    static public function getMetric($metric) 
    {
        $query = static::query('SELECT * FROM metric WHERE metric = :metric', array(':metric' => $metric));
        $obj = $query->fetchObject(__CLASS__);
        if(! ($obj instanceOf \Goteo\Model\Mail\Metric) ) {
            $obj = new self(['metric' => $metric]);
            $errors = [];
            if(!$obj->save($errors)) {
                throw new ModelException(implode("\n", $errors));
            }
        }
        return $obj;
    }

    /**
     * Guardar.
     * @param   type array $errors Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
     public function save(&$errors = array()) 
     {

        if(!$this->validate($errors) ) { return false; 
        }

        try {
            $this->dbInsertUpdate(['metric', 'desc'], ['metric']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Metric saving error: ' . $e->getMessage();
        }
        return false;

        }

        /**
     * Validar.
     * @param   type array $errors Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
        public function validate(&$errors = array()) 
        {
            if(empty($this->metric)) {
                $errors[] = 'Empty metric';
            }
            return empty($errors);
        }

}
