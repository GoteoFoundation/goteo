<?php

namespace Goteo\Model;

use Goteo\Core\Model;
use Symfony\Component\HttpFoundation\Request;

class FormHoneypot extends Model
{
    public $id;

    /**
     * This value should be left blank by humans but non-blank by robots
     */
    public $trap;

    /**
     * This value was, most-likely, introduced by a robot
     */
    public $prey;

    public $params;

    /**
     * The template for the trap field
     */
    public $template = 'partials/form/honeypot';

    protected $Table = 'form_honeypot';
    static protected $Table_static = 'impact_item';

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) return false;

        $this->dbInsertUpdate(['id', 'trap', 'prey', 'template']);
    }

    public function validate(&$errors = array())
    {
        if (empty($errors))
            return true;
        else
            return false;
    }

    /**
     * Get a trapped form field that is invisible to humans and juicy for robots to fill
     */
    public static function layTrap()
    {
        $honeypot = new FormHoneypot;
        $honeypot->trap = "email_addr_confirm";
        $honeypot->prey = "";
        $honeypot->params = [
            'trap' => $honeypot->trap,
            'prey' => $honeypot->prey
        ];

        return $honeypot;
    }

    /**
     * Checks if something got caught in the trap
     * @return bool `true` if caught something, `false` if not
     */
    public static function checkTrap(string $trap, $data): bool
    {
        if ($data instanceof Request) {
            return $data->request->get($trap) !== "";
        }
        
        return false;
    }
}
