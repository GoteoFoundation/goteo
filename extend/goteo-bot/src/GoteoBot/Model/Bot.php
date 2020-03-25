<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace GoteoBot\Model;

interface Bot {
    public function sendMessage($chadId, $text);
    public function sendImage($chatId, $image, $caption);
    public function sendAnimation($chatId, $animation, $caption);
}
