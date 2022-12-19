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

    const GLOBAL = "global";
    const PROJECT = "project";

	public
		$id,
		$title,
		$data,
        $data_unit,
		$description,
		$image,
		$lang,
        $type;

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
                    impact_data.type
                FROM impact_data
                $joins
                WHERE impact_data.id = :id";

        $impact_data = static::query($sql, [':id' => $id])->fetchObject(__CLASS__);

        if (!$impact_data instanceof ImpactData) {
            throw new ModelNotFoundException("[$id] not found");
        }

        return $impact_data;
    }

    public static function getList($filters = array(), int $offset = 0, int $limit = 10, int $count = 0) {
    	$sqlWhere = "";

        $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang);

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
                    impact_data.type
                FROM impact_data
                $joins
                $sqlWhere
                LIMIT $offset, $limit
            ";

        $query = static::query($sql);
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

		$fields = ['title','data', 'data_unit', 'description','image','lang', 'type'];

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
                self::GLOBAL,
                self::PROJECT
        ];
    }
}

