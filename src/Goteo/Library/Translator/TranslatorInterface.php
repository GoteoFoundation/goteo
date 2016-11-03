<?php

namespace Goteo\Library\Translator;

interface TranslatorInterface {
    static public function getFields($zone);
    static public function getOrders($zone);
    static public function get($zone, $id);
    static public function getList($zone, $filters = [], $offset = 0, $limit = 20, $count = false);

    public function getTranslation($lang, $field = null, $respect_original = false);
    public function getType();
    public function isOriginal($lang);
    public function isTranslated($lang);
    public function isPending($lang);
    public function delete($lang);
    public function save($lang, $values);
}
