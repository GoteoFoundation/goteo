<?php

namespace Goteo\Model\LegalEntity;

use Goteo\Library\Text;
use Goteo\Model\LegalEntity;
use Goteo\Model\LegalDocumentType;

class NaturalPerson extends LegalEntity {

    public function __construct() {
        $this->legal_entity = self::NATURAL_PERSON;
    }

    public function getLegalDocumentTypes(): array {
        return  [
            LegalDocumentType::NIF => Text::get('contract-legal-document-type-nif'),
            LegalDocumentType::NIE => Text::get('contract-legal-document-type-nie'),
            LegalDocumentType::PASSPORT => Text::get('contract-legal-document-type-passport'),
          ];
    }
}