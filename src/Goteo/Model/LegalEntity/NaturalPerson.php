<?php

namespace Goteo\Model\LegalEntity;

use Goteo\Library\Text;
use Goteo\Model\LegalEntity;

class NaturalPerson extends LegalEntity {

    public function __construct() {
        $this->legal_entity = self::NATURAL_PERSON;
    }

}