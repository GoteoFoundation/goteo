<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\ImpactData;

use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\Project;
use PDO;
use PDOException;

class ImpactDataProject extends Model {
    private ?ImpactData $impactData = null;
    private int $impact_data_id;
    private ?Project $project = null;
    private string $project_id;
    private int $estimationAmount = 0;
    private int $data = 0;

    protected $Table = 'impact_data_project';
    static protected $Table_static = 'impact_data_project';

    public function __construct()
    {
        if (isset($this->impact_data_id)) {
            $this->impactData = ImpactData::get($this->impact_data_id);
        }

        if (isset($this->project_id)) {
            $this->project = Project::get($this->project_id);
        }

        if (isset($this->estimation_amount)) {
            $this->estimationAmount = $this->estimation_amount;
        }
    }

    public function getImpactData(): ?ImpactData
    {
        return $this->impactData;
    }

    public function setImpactData(ImpactData $impactData): ImpactDataProject
    {
        $this->impactData = $impactData;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): ImpactDataProject
    {
        $this->project = $project;
        return $this;
    }

    public function getEstimationAmount(): int
    {
        return $this->estimationAmount;
    }

    public function setEstimationAmount(int $estimationAmount): ImpactDataProject
    {
        $this->estimationAmount = $estimationAmount;
        return $this;
    }

    public function getData(): ?int
    {
        return $this->data;
    }

    public function setData(int $data): ImpactDataProject
    {
        $this->data = $data;
        return $this;
    }

    static public function getByProjectAndImpactData(Project $project, ImpactData $impactData): ImpactDataProject
    {
        $table = self::$Table_static;

        $sql = "SELECT *
                FROM $table
                WHERE project_id = :project AND impact_data_id = :impact_data_id
        ";

        $impactDataProject = self::query($sql, [":project" => $project->id, ":impact_data_id" => $impactData->id])->fetchObject(__CLASS__);
        $impactDataProject->project = $project;
        $impactDataProject->impactData = $impactData;
        return $impactDataProject;
    }

    /**
     * @return ImpactDataProject[]
     */
    static public function getListByProject(Project $project): array
    {
        $table = self::$Table_static;

        $sql = "SELECT *
                FROM $table
                WHERE project_id = ?
        ";

        $list = [];
        try {
            foreach(self::query($sql, [$project->id])->fetchAll(\PDO::FETCH_OBJ) as $obj) {
                $impactDataProject = new ImpactDataProject();
                $impactData = ImpactData::get($obj->impact_data_id);

                $impactDataProject->setImpactData($impactData)->setProject($project)->setData($obj->data)->setEstimationAmount($obj->estimation_amount);
                $list[] = $impactDataProject;
            }
        } catch (\PDOException $e) {
            return [];
        }

        return $list;
    }

    /**
     * @return ImpactDataProject[]
     */
    static public function getCalculatedListByProject(Project $project): array
    {
        $table = self::$Table_static;

        $sql = "
            SELECT idp.impact_data_id, idp.project_id, SUM(c.amount) as estimation_amount, ipi.value as `data`
            FROM impact_data_project idp
            INNER JOIN impact_data_item idi ON idi.impact_data_id = idp.impact_data_id
            INNER JOIN impact_project_item ipi ON ipi.impact_item_id = idi.impact_item_id
            INNER JOIN impact_project_item_cost ipic ON ipic.impact_project_item_id = ipi.id
            INNER JOIN cost c ON c.id = ipic.cost_id
            WHERE idp.project_id  = :project_id AND ipi.project_id = :project_id
            GROUP BY idp.impact_data_id
            ";

        $list = [];
        try {
            foreach(self::query($sql, [':project_id' => $project->id])->fetchAll(\PDO::FETCH_OBJ) as $obj) {
                $impactDataProject = new ImpactDataProject();
                $impactData = ImpactData::get($obj->impact_data_id);

                $impactDataProject->setImpactData($impactData)->setProject($project)->setData($obj->data)->setEstimationAmount($obj->estimation_amount);
                $list[] = $impactDataProject;
            }
        } catch (\PDOException $e) {
            return [];
        }

        return $list;
    }

    /**
     * @return ImpactDataProject[]
     */
    static public function getListByProjectAndFootprint(Project $project, Footprint $footprint): array
    {
        $table = self::$Table_static;

        $sql = "SELECT *
                FROM $table
                INNER JOIN footprint_impact ON footprint_impact.impact_data_id = impact_data_project.impact_data_id
                WHERE project_id = :project and footprint_impact.footprint_id = :footprint
        ";
        $values = [
            ':project' => $project->id,
            'footprint' => $footprint->id
        ];

        $list = [];
        try {
            foreach(self::query($sql, $values)->fetchAll(\PDO::FETCH_OBJ) as $obj) {
                $impactDataProject = new ImpactDataProject();
                $impactData = ImpactData::get($obj->impact_data_id);

                $impactDataProject->setImpactData($impactData)->setProject($project)->setData($obj->data)->setEstimationAmount($obj->estimation_amount);
                $list[] = $impactDataProject;
            }
        } catch (\PDOException $e) {
            return [];
        }

        return $list;
    }

    static public function getCalculatedByProjectAndFootprint(Project $project, Footprint $footprint): array
    {
        $sql = "
            SELECT idp.impact_data_id, idp.project_id, SUM(c.amount) as estimation_amount, ipi.value as `data`
            FROM impact_data_project idp
            INNER JOIN footprint_impact fi on fi.impact_data_id = idp.impact_data_id
            INNER JOIN impact_data_item idi ON idi.impact_data_id = idp.impact_data_id
            INNER JOIN impact_project_item ipi ON ipi.impact_item_id = idi.impact_item_id
            INNER JOIN impact_project_item_cost ipic ON ipic.impact_project_item_id = ipi.id
            INNER JOIN cost c ON c.id = ipic.cost_id
            WHERE idp.project_id  = :project_id AND ipi.project_id = :project_id and fi.footprint_id = :footprint_id
            GROUP BY idp.impact_data_id
        ";

        $values = [
            ':project_id' => $project->id,
            ':footprint_id' => $footprint->id
        ];

        try {
            return self::query($sql, $values)->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        } catch (\PDOException $e) {
            return [];
        }
    }

    static public function count(Project $project): int
    {
        $table = self::$Table_static;

        $sql = "SELECT count(*)
                FROM $table
                WHERE project_id = ?
        ";

        return self::query($sql, [$project->id])->fetchColumn();
    }

    static public function exists(ImpactData $impactData, Project $project): bool
    {
        $table = self::$Table_static;

        $sql = "SELECT *
                FROM $table
                WHERE impact_data_id = :impact_data_id AND project_id = :project_id
        ";

        $values = [
            ':impact_data_id' => $impactData->id,
            ':project_id' => $project->id
        ];

        return (bool) self::query($sql, $values);
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) {
            var_dump($errors); die;
            return false;
        }

        $fields = [
            'impact_data_id' => ':impact_data_id',
            'project_id' => ':project_id',
            'estimation_amount' => ':estimation_amount',
            'data' => ':data'
        ];

        $values = [
            ':impact_data_id' => $this->impactData->id,
            ':project_id' => $this->project->id,
            ':estimation_amount' => $this->estimationAmount,
            ':data' => $this->data
        ];

        $sql = "REPLACE INTO `$this->Table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;
    }

    public function validate(&$errors = array())
    {
        if (empty($this->impactData)) {
            $errors['impact-data'] = Text::get('validate-missing-impact-data');
        }

        if (empty($this->project)) {
            $errors['project'] = Text::get('validate-missing-project');
        }

        if (empty($this->estimationAmount)) {
            $errors['estimation_amount'] = Text::get('validate-missing-estimation');
        }

        if (empty($this->data)) {
            $errors['data'] = Text::get('validate-missing-data');
        }

        return empty($errors);
    }
}
