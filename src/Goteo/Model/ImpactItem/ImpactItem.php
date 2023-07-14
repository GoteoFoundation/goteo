<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\ImpactItem;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\ImpactData;
use Goteo\Model\Project;
use PDO;
use PDOException;

class ImpactItem extends Model
{
    private int $id;
    private string $name;
    private string $description;
    private string $unit;

    protected $Table = 'impact_item';
    static protected $Table_static = 'impact_item';

    public static function getLangFields(): array
    {
        return [
            'name',
            'description',
            'unit'
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ImpactItem
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImpactItem
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ImpactItem
    {
        $this->description = $description;
        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): ImpactItem
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @throws ModelNotFoundException
     */
    static public function getById(int $id): ImpactItem
    {
        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $table = self::$Table_static;

        $sql = "
            SELECT
                `$table`.id,
                $fields
            FROM `$table`
            $joins
            WHERE `$table`.id = ?";
        $impactItem = self::query($sql, [$id])->fetchObject(ImpactItem::class);

        if (!$impactItem instanceOf ImpactItem)
            throw new ModelNotFoundException("ImpactItem with id $id not found");

        return $impactItem;
    }

    /**
     * @return ImpactItem[]
     */
    static public function getByImpactData(ImpactData $impactData): array
    {
        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $table = self::$Table_static;
        $sql = "
            SELECT `$table`.id,
                   $fields
            FROM `$table`
            $joins
            INNER JOIN impact_data_item ON impact_data_item.impact_item_id = `$table`.id
            WHERE impact_data_item.impact_data_id = ?
        ";

        return self::query($sql, [$impactData->id])->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return ImpactItem[]
     */
    static public function getByImpactDataAndProject(ImpactData $impactData, Project $project): array
    {
        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $table = self::$Table_static;
        $sql = "
            SELECT
                `$table`.id,
                $fields
            FROM `$table`
            $joins
            INNER JOIN impact_data_item ON impact_data_item.impact_item_id = `$table`.id
            INNER JOIN impact_project_item ON impact_project_item.impact_item_id = `$table`.id
            WHERE impact_data_item.impact_data_id = :impact_data_id AND impact_project_item.project_id = :project_id
        ";

        return self::query($sql, ["impact_data_id" => $impactData->id, ":project_id" => $project->id])->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }


    /**
     * @return ImpactItem[]
     */
    static public function getAll(): array
    {
        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);
        $table = self::$Table_static;

        $sql = "
            SELECT
                `$table`.id,
                $fields
            FROM $table
            $joins
        ";

        return self::query($sql)->fetchAll(PDO::FETCH_CLASS, ImpactItem::class);
    }


    public function save(&$errors = array()): bool
    {
        if (!$this->validate($errors)) return false;

        $fields = [
            'id' => ':id',
            'name' => ':name',
            'description' => ':description',
            'unit' => ':unit'
        ];

        try {
            $this->dbInsertUpdate($fields);
        } catch (PDOException $e) {
            $errors[] = "Error insert/update impact item " . $e->getMessage();
            return false;
        }

        return true;
    }

    public function validate(&$errors = array()): bool
    {
        if (!$this->name) {
            $errors['name'] = Text::get('validate-missing-name');
        }

        if (!$this->description) {
            $errors['description'] = Text::get('validate-missing-description');
        }

        if (!$this->unit) {
            $errors['unit'] = Text::get('validate-missing-unit');
        }

        return empty($errors);
    }
}
