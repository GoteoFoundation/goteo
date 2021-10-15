<?php

namespace Goteo\Model\LegalEntity;

use Goteo\Library\Text;
use Goteo\Model\LegalEntity;

class LegalPerson extends LegalEntity {

    public function __construct() {
        $this->legal_entity = self::LEGAL_PERSON;
    }

    public static function getLegalDocumentTypes(): array {
        return  [
            self::CIF => Text::get('contract-legal-document-type-cif')
          ];
    }
}