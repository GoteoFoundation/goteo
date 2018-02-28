<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Util\ModelNormalizer\Transformer;

interface TransformerInterface {
    public function getDefaultKeys();
    public function getModelName();
    public function getLabel($key);
    public function getValue($key);
    public function getName();
    public function getLink($type = 'public');
}
