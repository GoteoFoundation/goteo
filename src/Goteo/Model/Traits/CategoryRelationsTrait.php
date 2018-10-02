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
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with Category relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_category" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "category_id" as relationship field name pointing to category.id
 */
trait CategoryRelationsTrait {

    public function getCategoryRelationalTable() {
        $tb = strtolower($this->getTable());
        if($tb === 'footprint')
            return "category_{$tb}";
        return "{$tb}_category";
    }

    /**
     * Add categories
     * @param [type]  $categories  category or array of categories
     */
    public function addCategories($categories, $remove_others=false) {
        if(!is_array($categories)) $categories = [$categories];

        $inserts = [];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($categories as $category) {
            if($category instanceOf Category) {
                $category = $category->id;
            }
            $inserts[] = "(:id, :category$i)";
            $deletes[] = ":category$i";
            $values[":category$i"] = $category;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getCategoryRelationalTable();
        $sql1 = "DELETE FROM `$rel` WHERE {$tb}_id=:id AND category_id NOT IN (" . implode(', ', $deletes ?: ['0']) .")";
        $sql2 = "INSERT IGNORE INTO `$rel` ({$tb}_id, category_id) VALUES " . implode(', ', $inserts);
        try {
            if($remove_others) {
                self::query($sql1, $values);
            }
            if($deletes) {
                self::query($sql2, $values);
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add categories: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Like removing all categories associated and add the specified
     * @return [type] [description]
     */
    public function replaceCategories($categories) {
        return $this->addCategories($categories, true);
    }

    /**
     * Return categories
     * @return [type] [description]
     */
    public function getCategories($lang = null) {
        $tb = strtolower($this->getTable());
        $rel = $this->getCategoryRelationalTable();

        list($fields, $joins) = Category::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                category.id,
                $fields,
                category.social_commitment
            FROM `$rel`
            INNER JOIN category ON category.id = `$rel`.category_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
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
        $rel = $this->getCategoryRelationalTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND category_id IN (" . implode(', ', $deletes) . ")";
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
