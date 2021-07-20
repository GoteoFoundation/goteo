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

	private
		$id,
		$title,
		$description,
		$image,
		$lang;

    protected $Table = 'impact_data';
	static protected $Table_static = 'impact_data';

    public static function getLangFields() {
        return ['title', 'description'];
    }

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id) {
		$this->id = $id;
	}


	public function getTitle(): string {
		return $this->title;
	}

	public function setTitle(string $title) {
		$this->title = $title;
	}


	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription(string $description) {
		$this->description = $description;
	}


	public function getLang(): string {
		return $this->lang;
	}

	public function setLang(string $lang) {
		$this->lang = $lang;
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

