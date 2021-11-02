<?php

namespace Goteo\Model\LegalDocumenType;

class VAT extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::VAT;
    }
}