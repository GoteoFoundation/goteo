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
use Goteo\Model\SocialCommitment;
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with SocialCommitment relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_social_commitment" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "social_commitment_id" as relationship field name pointing to social_commitment.id
 */
trait SocialCommitmentRelationsTrait {

    public function getSocialCommitmentRelationalTable() {
        $tb = strtolower($this->getTable());
        if($tb === 'footprint')
            return "social_commitment_{$tb}";
        return "{$tb}_social_commitment";
    }

    /**
     * Add social_commitments
     * @param [type]  $social_commitments  social_commitment or array of social_commitments
     */
    public function addSocialCommitments($social_commitments, $remove_others=false) {
        if(!is_array($social_commitments)) $social_commitments = [$social_commitments];

        $inserts = [];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($social_commitments as $social_commitment) {
            if($social_commitment instanceOf SocialCommitment) {
                $social_commitment = $social_commitment->id;
            }
            $inserts[] = "(:id, :social_commitment$i)";
            $deletes[] = ":social_commitment$i";
            $values[":social_commitment$i"] = $social_commitment;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getSocialCommitmentRelationalTable();
        $sql1 = "DELETE FROM `$rel` WHERE {$tb}_id=:id AND social_commitment_id NOT IN (" . implode(', ', $deletes ?: ['0']) .")";
        $sql2 = "INSERT IGNORE INTO `$rel` ({$tb}_id, social_commitment_id) VALUES " . implode(', ', $inserts);
        try {
            if($remove_others) {
                self::query($sql1, $values);
            }
            if($deletes) {
                self::query($sql2, $values);
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add social_commitments: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Like removing all social commitments associated and add the specified
     * @return [type] [description]
     */
    public function replaceSocialCommitments($social_commitments) {
        return $this->addSocialCommitments($social_commitments, true);
    }

    /**
     * Return social_commitments
     * @return [type] [description]
     */
    public function getSocialCommitments($lang = null) {
        $tb = strtolower($this->getTable());
        $rel = $this->getSocialCommitmentRelationalTable();
        list($fields, $joins) = SocialCommitment::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                social_commitment.id,
                social_commitment.icon,
                $fields
            FROM `$rel`
            INNER JOIN social_commitment ON social_commitment.id = `$rel`.social_commitment_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
        $values = [':id' => $this->id];
        if($query = self::query($sql, $values)) {
            if( $social_commitments = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\SocialCommitment') ) {
                return $social_commitments;
            }
        }
        return [];
    }

    /**
     * Delete social_commitments
     * @param [type]  $social_commitments  social_commitment or array of social_commitments
     */
    public function removeSocialCommitments($social_commitments) {
        if(!is_array($social_commitments)) $social_commitments = [$social_commitments];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($social_commitments as $social_commitment) {
            if($social_commitment instanceOf SocialCommitment) {
                $social_commitment = $social_commitment->id;
            }
            $deletes[] = ":social_commitment$i";
            $values[":social_commitment$i"] = $social_commitment;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getSocialCommitmentRelationalTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND social_commitment_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove social_commitments: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return main social_commitment
     */
    public function getMainSocialCommitment() {
        return $this->getSocialCommitments() ? current($this->getSocialCommitments()) : null;
    }

}
