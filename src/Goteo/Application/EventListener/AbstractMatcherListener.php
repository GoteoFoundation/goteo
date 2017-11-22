<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Goteo\Model\Matcher;

abstract class AbstractMatcherListener extends AbstractListener {

    public function hasAppListener(Matcher $matcher) {
        if($processor = $this->getService('app.matcher.finder')->getProcessor($matcher)) {
            return array_key_exists(get_called_class(), $processor::getAppEventListeners());
        }
        return false;
    }

    public function hasConsoleListener(Matcher $matcher) {
        if($processor = $this->getService('app.matcher.finder')->getProcessor($matcher)) {
            return array_key_exists(get_called_class(), $processor::getConsoleEventListeners());
        }
        return false;
    }

}
