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

    /**
     * Add social_commitments
     * @param [type]  $social_commitments  social_commitment or array of social_commitments
     */
    public function addSocialCommitments($social_commitments) {
        if(!is_array($social_commitments)) $social_commitments = [$social_commitments];

        $inserts = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($social_commitments as $social_commitment) {
            if($social_commitment instanceOf SocialCommitment) {
                $social_commitment = $social_commitment->id;
            }
            $inserts[] = "(:id, :social_commitment$i)";
            $values[":social_commitment$i"] = $social_commitment;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "INSERT IGNORE INTO `{$tb}_social_commitment` ({$tb}_id, social_commitment_id) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add social_commitments: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return social_commitments
     * @return [type] [description]
     */
    public function getSocialCommitments($lang = null) {
        $tb = strtolower($this->getTable());
        list($fields, $joins) = SocialCommitment::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                social_commitment.id,
                social_commitment.icon,
                $fields
            FROM {$tb}_social_commitment
            INNER JOIN social_commitment ON social_commitment.id = {$tb}_social_commitment.social_commitment_id
            $joins
            WHERE {$tb}_social_commitment.{$tb}_id = :id
            ORDER BY {$tb}_social_commitment.order ASC";
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
        $sql = "DELETE FROM `{$tb}_social_commitment` WHERE {$tb}_id = :id AND social_commitment_id IN (" . implode(', ', $deletes) . ")";
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
