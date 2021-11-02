<?php

namespace Goteo\Model\LegalDocumentType;

use Goteo\Model\LegalDocumentType;

class PASSPORT extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::PASSPORT;
    }
}