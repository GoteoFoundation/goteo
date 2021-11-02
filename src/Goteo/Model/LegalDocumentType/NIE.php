<?php

namespace Goteo\Model\LegalDocumenType;

class NIE extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::NIE;
    }
}