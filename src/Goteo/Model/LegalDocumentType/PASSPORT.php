<?php

namespace Goteo\Model\LegalDocumenType;

class PASSPORT extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::PASSPORT;
    }
}