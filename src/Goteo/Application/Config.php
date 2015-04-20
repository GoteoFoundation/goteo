<?php

namespace Goteo\Application;

class Config {

    static function isNode() {
        return NODE_ID !== GOTEO_NODE;
    }

    static function getVar($var) {
        if($var == 'title') return self::isNode() ? NODE_META_TITLE : GOTEO_META_TITLE;
        if($var == 'meta_description') return self::isNode() ? NODE_META_DESCRIPTION : GOTEO_META_DESCRIPTION;
        if($var == 'meta_keyword') return self::isNode() ? NODE_META_KEYWORDS : GOTEO_META_KEYWORDS;
        if($var == 'meta_author') return self::isNode() ? NODE_META_AUTHOR : GOTEO_META_AUTHOR;
        if($var == 'meta_copyright') return self::isNode() ? NODE_META_COPYRIGHT : GOTEO_META_COPYRIGHT;
        return '';
    }
}
