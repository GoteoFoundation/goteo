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

use Goteo\Core\Model as CoreModel;
use Goteo\Model;
use Goteo\Util\ModelNormalizer\Transformer;

/**
 * This class allows to get an object standarized for its use in views
 */
class ModelNormalizer {
    private $model;
    private $keys;

    public function __construct(CoreModel $model,array $keys = null) {
        $this->model = $model;
        $this->keys = $keys;
    }

    /**
     * Returns the normalized object
     * @return Goteo\Util\ModelNormalizer\TransformerInterface
     */
    public function get() {
        if($this->model instanceOf Model\User) {
            return new Transformer\UserTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Blog\Post) {
            return new Transformer\PostTransformer($this->model, $this->keys);
        }
        else return new Transformer\GenericTransformer($this->model, $this->keys);
    }
}
