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

use \React\EventLoop\Factory;
use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\TgLog;
use \unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use \unreal4u\TelegramAPI\Telegram\Methods\SetWebhook;

use Goteo\Application\Config;

Class TelegramBot implements Bot {

    const PLATFORM = "telegram";
    const URL = "t.me";

    private
        $loop,
        $handler,
        $tgLog;

    public function createBot() {
        $this->loop = Factory::create();
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

    public function setWebhook() {
        $setWebhook = new SetWebhook();
        $setWebhook->url = Config::get('url.main') . '/telegram/' . Config::get('bot.telegram.token');

        $this->tgLog = new TgLog(Config::get('bot.telegram.token'), new HttpClientRequestHandler($this->loop));
        $this->tgLog->performApiRequest($setWebhook);
        $this->loop->run();
    }

    public static function getName() {
        return Config::get('bot.telegram.name');
    }
}
