<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Bot;

use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\TgLog;
use \unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use Goteo\Application\Config;

Class TelegramBot implements Bot {

    private
        $loop,
        $handler,
        $tgLog;

    public function createBot() {
        $this->loop = \React\EventLoop\Factory::create();
        $this->handler = new HttpClientRequestHandler($this->loop);
        try {
            $this->tgLog = new TgLog(Config::get('bot.telegram.token'), $this->handler);
        } catch(ConfigException $e) {
            Message::error($e->getMessage());
        }
    }

    public function sendMessage($chatId, $text) {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chatId;
        $sendMessage->text = $text;
        $this->tgLog->performApiRequest($sendMessage);
        $this->loop->run();
    }
}
