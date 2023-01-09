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

class ImpactData extends Model {

    const TYPE_ESTIMATION = "estimation";
    const TYPE_REAL = "real";

    const SOURCE_ITEM = "item";
    const SOURCE_PROJECT = "project";
    const SOURCE_CHANNEL = "channel";

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

    protected $Table = 'impact_data';
	static protected $Table_static = 'impact_data';

    public static function getLangFields(): array
    {
        return ['title', 'data', 'data_unit', 'description'];
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
                    impact_data.source
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

        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        if ($filters['source']) {
            $sqlWhere[] = "impact_data.source = :source";
            $values[':source'] = $filters['source'];
        }

        if ($filters['type']) {
            $sqlWhere[] = "impact_data.type = :type";
            $values[':type'] = $filters['type'];
        }

        $sqlWhere = $sqlWhere ? "WHERE " . implode(' AND ', $sqlWhere) : '';

        if ($count) {
            $sql = "SELECT COUNT(impact_data.id)
            FROM impact_data
            $sqlWhere";
            return (int) self::query($sql)->fetchColumn();
        }

        $sql = "SELECT
                    impact_data.id,
                    $fields,
                    impact_data.image,
                    impact_data.lang,
                    impact_data.type,
                    impact_data.icon,
                    impact_data.source
                FROM impact_data
                $joins
                $sqlWhere
                LIMIT $offset, $limit
            ";

        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
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

        if (!$this->data)
            $errors['data'] = Text::get('mandatory-data');

        if (!$this->data_unit)
            $errors['data'] = Text::get('mandatory-data-unit');

        if (!$this->description)
            $errors['description'] = Text::get('mandatory-description');

		return empty($errors);
	}

	public function save(&$errors = array()): bool
    {
        if(!$this->validate($errors)) return false;

		$fields = ['title','data', 'data_unit', 'description','image','lang', 'type', 'icon', 'source'];

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
}

