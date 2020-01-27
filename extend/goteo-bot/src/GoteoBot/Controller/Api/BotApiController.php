<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace GoteoBot\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use \unreal4u\TelegramAPI\Telegram\Types\Update;
use GoteoBot\Model\ProjectBot;
use GoteoBot\Model\Bot\TelegramBot;
use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerException;
use Goteo\Core\View\Exception;

class BotApiController extends \Goteo\Controller\Api\AbstractApiController {
    protected $is_admin = false;
    protected $user = null;
    protected $telegram_token = null;

    public function __construct() {
        // changing to a json theme here (not really a theme)
        View::setTheme('JSON');

        $this->telegram_token = Config::get('bot.telegram.token');
    }

    public function validateToken($token) {

        if(empty($this->telegram_token)) throw new ControllerException("Please configure telegram token in settings");
        if($this->telegram_token !== $token) throw new ControllerException("Invalid token");
    }

    /**
     * Get Update from webhook
     */
    public function getUpdate($token, Request $request) {
        $this->validateToken($token);
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
            $update = new Update($data);
            
            if (substr( $update->message->text, 0, 6 ) === "/start") {

                $project = \mybase64_decode(explode('/start', $update->message->text)[1]);
                if (!$project) 
                    return $this->jsonResponse(['error' => 'no project with this id']);
    
                if ($project_bot = ProjectBot::get($project)) {
                    if ($project_bot->channel_id == $update->message->chat->id) {
                        return $this->jsonResponse(['error' => 'bot already exists']);
                    }
                }
                
                $project_bot = new ProjectBot();
                $project_bot->project = $project;
                $project_bot->platform = TelegramBot::PLATFORM;
                $project_bot->channel_id = $update->message->chat->id;
                $project_bot->save();
    
                $bot = new TelegramBot();
                $bot->createBot();
                $bot->sendMessage($project_bot->channel_id, Text::get('telegram-welcome-channel-project', Project::get($project_bot->project)->name));
            }
            
        }

        return $this->jsonResponse([]);
    }

}

