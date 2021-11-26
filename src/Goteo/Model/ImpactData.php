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

use Goteo\Core\Model;
use Goteo\Model\Image;
use Goteo\Library\Text;

use Goteo\Application\Exception\ModelNotFoundException;

class ImpactData extends Model {

	public
		$id,
		$title,
		$data,
        $data_unit,
		$description,
		$image,
		$lang;

    protected $Table = 'impact_data';
	static protected $Table_static = 'impact_data';

    public static function getLangFields() {
        return ['title', 'data', 'data_unit', 'description'];
    }

    public static function get($id) {

        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql = "SELECT
                    id,
                    $fields,
                    image,
                    lang
                FROM impact_data
                $joins
                WHERE id = :id";

        $impact_data = static::query($sql, [':id' => $id])->fetchObject(__CLASS__);

        if (!$impact_data instanceof ImpactData) {
            throw new ModelNotFoundException("[$id] not found");
        }

        return $impact_data;
    }

    public static function getList($filters = array(), int $offset = 0, int $limit = 10, int $count = 0) {
    	$sqlWhere = "";

        list($fields, $joins) = self::getLangsSQLJoins($lang);

        if ($count) {
            $sql = "SELECT COUNT(impact_data.id)
            FROM impact_data
            $sqlWhere";
            return (int) self::query($sql)->fetchColumn();
        }

        $sql = "SELECT
                    id,
                    $fields
                    image,
                    lang
                FROM impact_data
                $joins
                $sqlWhere
                LIMIT $offset, $limit
            ";

        $query = static::query($sql);
        $impact_data = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        return $impact_data;
    }

	public function getImage(): Image {
		return Image::get($this->image);
	}

	public function setImage(Image $image) {
		$this->image = $image->getName();
	}

	public function validate(&$errors = array()) {
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

	public function save(&$errors = array()) {

        if(!$this->validate($errors)) return false;

		$fields = ['title','data', 'data_unit', 'description','image','lang'];

		try {
            $this->dbInsertUpdate($fields);
        } catch(\PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;

	}
}

