<?php

namespace Goteo\Model;

use Goteo\Library\Text;

use Goteo\Core\Model;
use Goteo\Model\LegalEntity\NaturalPerson;
use Goteo\Model\LegalEntity\LegalPerson;

abstract class LegalEntity {

    const NATURAL_PERSON = 'natural_person';
    const LEGAL_PERSON = 'legal_person';
    const LEGAL_ENTITIES = [self::NATURAL_PERSON, self::LEGAL_PERSON];

    protected string $legal_entity;

    public static function create(string $legal_entity = ''): LegalEntity {
        switch ($legal_entity) {
            case self::LEGAL_PERSON:
                return new LegalPerson();
            default:
                return new NaturalPerson();
        }
    }

    public function getEntity(): string {
        return $this->legal_entity;
    }

    public static function getLegalEntities(): array {
        return  [
              self::NATURAL_PERSON => Text::get('donor-legal-entities-natural-person'),
              self::LEGAL_PERSON => Text::get('donor-legal-entities-legal-person')
            ];
    }
}