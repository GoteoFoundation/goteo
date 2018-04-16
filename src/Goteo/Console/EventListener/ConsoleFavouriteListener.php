<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\AppEvents;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Application\Config;
use Goteo\Model\Event;
use Goteo\Model\Project;
use Goteo\Model\Project\Favourite;
use Goteo\Application\Currency;
use Goteo\Model\Mail;
use Goteo\Model\Template;


class ConsoleFavouriteListener extends AbstractListener {

    /**
     *
     * @param  FilterProjectEvent $event
     */
    public function onProjectActive(FilterProjectEvent $event) {
        $project = $event->getProject();

        $day=$event->getDays();

        if($project->amount<$project->mincost)
        {

            $users=Favourite::usersSentToday($project->id);

            if(!empty($users)){

                $this->info("Sending mail to users with favourite project");

                foreach ($users as $user) {

                    // Send mail with favourite

                    $days_left=40-$day;
                    $money_left=Currency::amountFormat($project->mincost-$project->amount);

                    if( Mail::createFromTemplate($user->user_email, $user->user_name, Template::FAVOURITE_PROJECT_REMEMBER, [
                            '%PROJECTNAME%'   => $project->name,
                            '%USERNAME%'   => $user->user_name,
                            '%PROJECTURL%'   => Config::getUrl($user->user_lang) . '/project/'.$project->id,
                            '%DAYS_LEFT%'   => $days_left,
                            '%MONEY_LEFT%'   => $money_left

                            ], $user->user_lang)
                    ->send($errors)) {
                        // Sent succesfully
                     }
                    else {
                        $vars['error'] .= implode("\n", $errors);
                    }

                }

            }
        }
    }

	public static function getSubscribedEvents() {
		return array(
            ConsoleEvents::PROJECT_ACTIVE    => 'onProjectActive'
		);
	}
}
