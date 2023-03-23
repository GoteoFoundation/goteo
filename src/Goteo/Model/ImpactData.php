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
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Project;

class ImpactData extends Model {
    const SOURCE_ITEM = "item";
    const SOURCE_PROJECT = "project";
    const SOURCE_CHANNEL = "channel";
    const SOURCE_MANUAL = "manual";

    const TYPE_ESTIMATION = "estimation";
    const TYPE_REAL = "real";

    const OPERATION_AMOUNT_ESTIMATION_DIVIDE_DATA = 'amount_estimation_divide_data';
    const OPERATION_DATA_DIVIDE_AMOUNT_ESTIMATION = 'data_divide_amount_estimation';

	public
		$id,
		$title,
		$data,
        $data_unit,
		$description,
		$image,
		$lang;

    public string $type = self::TYPE_ESTIMATION;
    public string $source = self::SOURCE_ITEM;
    public ?string $icon = null;
    public ?string $result_msg = null;
    public ?string $operation_type = null;

    protected $Table = 'impact_data';
	static protected $Table_static = 'impact_data';

    public static function getLangFields(): array
    {
        return ['title', 'data', 'data_unit', 'description', 'result_msg'];
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function get($id): ImpactData
    {
        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql = "SELECT
                    impact_data.id,
                    $fields,
                    impact_data.image,
                    impact_data.lang,
                    impact_data.type,
                    impact_data.icon,
                    impact_data.source,
                    impact_data.result_msg,
                    impact_data.operation_type
                FROM impact_data
                $joins
                WHERE impact_data.id = ?";

        $impact_data = static::query($sql, $id)->fetchObject(__CLASS__);

        if (!$impact_data instanceof ImpactData) {
            throw new ModelNotFoundException("[$id] not found");
        }

        return $impact_data;
    }

    public static function getList(array $filters = [], int $offset = 0, int $limit = 10, int $count = 0) {
    	$sqlWhere = [];
        $values = [];
        $sqlInner = "";

        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        if ($filters['source']) {
            if (is_array($filters['source'])) {
                $parts = [];
                foreach($filters['source'] as $i => $source) {
                    $parts[] = ':source' . $i;
                    $values[':source' . $i] = $source;
                }

                if($parts) $sqlWhere[] = "impact_data.source IN (" . implode(',', $parts) . ")";
            } else {
                $sqlWhere[] = "impact_data.source = :source";
                $values[':source'] = $filters['source'];
            }
        }

        if ($filters['not_source']) {
            $sqlWhere[] = "impact_data.source != :not_source";
            $values[':not_source'] = $filters['not_source'];
        }

        if ($filters['type']) {
            if (is_array($filters['type'])) {
                $parts = [];
                foreach($filters['type'] as $i => $type) {
                    $parts[] = ':type' . $i;
                    $values[':type' . $i] = $type;
                }

                if($parts) $sqlWhere[] = "impact_data.type IN (" . implode(',', $parts) . ")";
            } else {
                $sqlWhere[] = "impact_data.type = :type";
                $values[':type'] = $filters['type'];
            }
        }

        if ($filters['not_type']) {
            $sqlWhere[] = "impact_data.type != :not_type";
            $values[':not_type'] = $filters['not_type'];
        }

        if ($filters['project']) {
            $sqlInner .= "INNER JOIN impact_data_project ON impact_data.id = impact_data_project.impact_data_id ";
            $sqlWhere[] = "impact_data_project.project_id = :project";
            $values[':project'] = $filters['project'];
        }

        if ($filters['footprint']) {
            $sqlInner.= "INNER JOIN footprint_impact ON impact_data.id = footprint_impact.impact_data_id ";
            $sqlWhere[] = "footprint_impact.footprint_id = :footprint";
            $values[':footprint'] = $filters['footprint'];
        }

        if ($sqlWhere) $sqlWhere = "WHERE " . implode( ' AND ', $sqlWhere);
        else $sqlWhere = "";

        if ($count) {
            $sql = "SELECT COUNT(impact_data.id)
            FROM impact_data
            $sqlInner
            $sqlWhere";

            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    impact_data.id,
                    $fields,
                    impact_data.image,
                    impact_data.lang,
                    impact_data.type,
                    impact_data.icon,
                    impact_data.source,
                    impact_data.operation_type
                FROM impact_data
                $joins
                $sqlInner
                $sqlWhere
                LIMIT $offset, $limit
            ";

        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return ImpactItem[]
     */
    public function getImpactItemsRelatedToProject(Project $project): array
    {
        return ImpactItem::getByImpactDataAndProject($this, $project);
    }

	public function getImage(): Image {
		return Image::get($this->image);
	}

	public function setImage(Image $image) {
		$this->image = $image->getName();
	}

	public function validate(&$errors = array()): bool
    {
		if (!$this->title)
			$errors['title'] = Text::get('mandatory-title');

        if (!$this->data_unit)
            $errors['data_unit'] = Text::get('mandatory-data-unit');

		return empty($errors);
	}

	public function save(&$errors = array()): bool
    {
        if(!$this->validate($errors)) return false;

		$fields = ['title','data', 'data_unit', 'description','image','lang', 'type', 'icon', 'source', 'result_msg', 'operation_type'];

		try {
            $this->dbInsertUpdate($fields);
        } catch(\PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;
	}

    public static function getTypes(): array
    {
        return [
                self::TYPE_ESTIMATION,
                self::TYPE_REAL
        ];
    }

    public static function getSources(): array
    {
        return [
            self::SOURCE_ITEM,
            self::SOURCE_PROJECT,
            self::SOURCE_CHANNEL
        ];
    }

    public static function getOperations(): array
    {
        return [
            self::OPERATION_AMOUNT_ESTIMATION_DIVIDE_DATA,
            self::OPERATION_DATA_DIVIDE_AMOUNT_ESTIMATION
            ];
    }
}

