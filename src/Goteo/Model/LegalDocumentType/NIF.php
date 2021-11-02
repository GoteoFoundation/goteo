<?php

namespace Goteo\Model\LegalDocumenType;

class NIF extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::NIF;
    }
}