<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Util\ModelNormalizer;

use Goteo\Util\ModelNormalizer\Transformer;
use Goteo\Application\Session;
/**
 * This class allows to get an object standarized for its use in views
 */
class EntityNormalizer {
    protected $model;
    protected $keys;

    public function __construct($model,array $keys = null) {
        $this->model = $model;
        $this->keys = $keys;
    }

    /**
     * Returns the normalized object
     * @return Goteo\Util\ModelNormalizer\TransformerInterface
     */
    public function get() {
        $ob->setUser(Session::getUser())->rebuild();

        return $ob;
    }
}
