<?php

namespace Goteo\Model\LegalEntity;

use Goteo\Library\Text;
use Goteo\Model\LegalEntity;

class NaturalPerson extends LegalEntity {

    public function __construct() {
        $this->legal_entity = self::NATURAL_PERSON;
    }

    public function getLegalDocumentTypes(): array {
        return  [
            self::NIF => Text::get('contract-legal-document-type-nif'),
            self::NIE => Text::get('contract-legal-document-type-nie'),
            self::PASSPORT => Text::get('contract-legal-document-type-passport'),
          ];
    }
}