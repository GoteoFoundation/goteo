<?php

namespace Goteo\Model\LegalEntity;

use Goteo\Library\Text;
use Goteo\Model\LegalEntity;

class LegalPerson extends LegalEntity {

    public function __construct() {
        $this->legal_entity = self::LEGAL_PERSON;
    }

}