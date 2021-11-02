<?php

namespace Goteo\Model\LegalDocumenType;

class CIF extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::CIF;
    }
}