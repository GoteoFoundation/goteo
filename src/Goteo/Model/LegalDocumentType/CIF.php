<?php

namespace Goteo\Model\LegalDocumentType;

class CIF extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::CIF;
    }
}