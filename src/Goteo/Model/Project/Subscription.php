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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;
use Goteo\Model\Project;

class Subscription extends Model
{
    public int $id = 0;

    /**
     * The id of the Project model this subscription belongs to
     */
    public string $project;

    /**
     * The name for this Subscription, shown on goteo and the billing platform (stripe, etc)
     */
    public string $name = "";

    /**
     * A text description for this Subscription
     */
    public string $description = "";

    /**
     * The currency amount that this Subscription charges
     */
    public int $amount = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project->id;

        return $this;
    }

    public function getProject(): ?Project
    {
        try {
            return Project::get($this->project);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) return false;

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate(['id', 'project', 'name', 'description', 'amount']);
            return true;
        } catch (\PDOException $e) {
            $errors[] = "Subscription save error: " . $e->getMessage();
            return false;
        }
    }

    public function validate(&$errors = array())
    {
        // Estos son errores que no permiten continuar
        if (empty($this->project))
            $errors[] = 'No hay proyecto al que asignar la subscripción';

        //cualquiera de estos errores hace fallar la validación
        if (!empty($errors))
            return false;
        else
            return true;
    }

    public static function getAll(Project $project)
    {
        try {
            $sql = "SELECT *
                    FROM subscription
                    WHERE subscription.project = :project
                    ORDER BY subscription.id ASC
                    ";

            $array = [];
            foreach (self::query($sql, [':project' => $project->id])->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $array[$item->id] = $item;
            }

            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }
}
