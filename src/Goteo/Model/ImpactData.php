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

class ImpactData extends Model {

	public
		$id,
		$title,
		$subtitle,
		$description,
		$image,
		$lang;

    protected $Table = 'impact_data';
	static protected $Table_static = 'impact_data';

    public static function getLangFields() {
        return ['title', 'subtitle', 'description'];
    }

    public function getList($filters = array(), int $offset = 0, int $limit = 0, int $count = 0) {
    	$sqlWhere = "";

        if ($count) {
            $sql = "SELECT COUNT(impact_data.id)
            FROM impact_data
            $sqlWhere";
            return (int) self::query($sql)->fetchColumn();
        }

        $sql = "SELECT *
                FROM impact_data
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
		if (!$this->title) {
			$errors['title'] = Text::get('mandatory-title');
		}

		if (!$this->description) {
			$errors['description'] = Text::get('mandatory-description');
		}

		return empty($errors);
	}

	public function save(&$errors = array()) {

        if(!$this->validate($errors)) return false;

		$fields = array(
            'id',
            'title',
            'subtitle',
            'description',
            'lang'
        );

		try {
            $this->dbInsertUpdate($fields);
        } catch(\PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;

	}
}

