<?php

namespace Goteo\Model\LegalDocumentType;

class NIE extends LegalDocumentType {

    public function __construct() {
        $this->document_type = self::NIE;
    }
}