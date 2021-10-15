<?php

namespace Goteo\Model;

use Goteo\Library\Text;

use Goteo\Core\Model;
use Goteo\Model\LegalEntity\NaturalPerson;
use Goteo\Model\LegalEntity\LegalPerson;

abstract class LegalEntity {

    const NATURAL_PERSON = 'natural_person';
    const LEGAL_PERSON = 'legal_person';

    private string $legal_entity;

    public static function create(string $legal_entity): LegalEntity {
        $legal_entity_type;

        switch ($legal_entity) {
            case self::LEGAL_PERSON:
                $legal_entity_type = new LegalPerson();
            case self::NATURAL_PERSON:
                $legal_entity_type = new NaturalPerson();
            default:
                $legal_entity_type = new NaturalPerson();
        }

        return $legal_entity_type;
    }

    public function getEntity(): string {
        return $this->legal_entity;
    }
    
    public static function getNaturalPerson(): string {
        return self::NATURAL_PERSON;
    }

    public static function getLegalPerson(): string {
        return self::LEGAL_PERSON;
    }

    public static function getLegalEntities(): array {
        return  [
              self::NATURAL_PERSON => Text::get('donor-legal-entities-natural-person'),
              self::LEGAL_PERSON => Text::get('donor-legal-entities-legal-person')
            ];
    }

}