<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Traits;

use Goteo\Application\Config;
use Goteo\Model\Category;

/**
 * Adds function to deal with Category relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_category" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "category_id" as relationship field name pointing to category.id
 */
trait CategoryRelationsTrait {

    /**
     * Add categories
     * @param [type]  $categories  category or array of categories
     */
    public function addCategories($categories) {
        if(!is_array($categories)) $categories = [$categories];

        $inserts = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($categories as $category) {
            if($category instanceOf Category) {
                $category = $category->id;
            }
            $inserts[] = "(:id, :category$i)";
            $values[":category$i"] = $category;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "INSERT INTO `{$tb}_category` ({$tb}_id, category_id) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add categories: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return categories
     * @return [type] [description]
     */
    public function getCategories($lang = null) {
        $tb = strtolower($this->getTable());

        list($fields, $joins) = Category::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                category.id,
                $fields,
                category.social_commitment
            FROM {$tb}_category
            INNER JOIN category ON category.id = {$tb}_category.category_id
            $joins
            WHERE {$tb}_category.{$tb}_id = :id
            ORDER BY {$tb}_category.order ASC";
        $values = [':id' => $this->id];
        if($query = self::query($sql, $values)) {
            if( $categories = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Category') ) {
                return $categories;
            }
        }
        return [];
    }

    /**
     * Delete categories
     * @param [type]  $categories  category or array of categories
     */
    public function removeCategories($categories) {
        if(!is_array($categories)) $categories = [$categories];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($categories as $category) {
            if($category instanceOf Category) {
                $category = $category->id;
            }
            $deletes[] = ":category$i";
            $values[":category$i"] = $category;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "DELETE FROM `{$tb}_category` WHERE {$tb}_id = :id AND category_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove categories: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return main sphere
     */
    public function getMainCategory() {
        return $this->getCategories() ? current($this->getCategories()) : null;
    }

}
