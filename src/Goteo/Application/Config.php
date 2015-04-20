<?php

namespace Goteo\Application;

class Config {

    static function isNode() {
        return NODE_ID !== GOTEO_NODE;
    }

}
