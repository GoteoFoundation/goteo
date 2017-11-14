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

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Library\Currency;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Util\MatcherProcessor\MatcherProcessorException;

//

class InvestMatcherListener extends AbstractMatcherListener {

	public function onInvestSuccess(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$response = $event->getResponse();
		$invest   = $method->getInvest();
		$project  = $invest->getProject();

        // Only for invests on projects
        if(!$project) return;

        // Check if project is in a matcher campaign
        if($matchers = Matcher::getFromProject($project)) {
            foreach($matchers as $matcher) {

                // Do not execute this listener if not required by the processor
                if(!$this->processorHasListener($matcher)) continue;

                // find processor, and execute it
                if($processor = $this->getService('app.matcher.finder')->getProcessor($matcher)) {
                    $processor->setInvest($invest);
                    $processor->setProject($project);
                    $processor->setMethod($method);
                    try {
                        $invests = $processor->getInvests();
                        foreach($invests as $drop) {
                            $errors = [];
                            // se actualiza el registro de convocatoria
                            if ($drop->save($errors)) {
                                Invest::query("UPDATE invest SET droped = :drop, `matcher`= :matcher WHERE id = :id",
                                    array(':id' => $invest->id, ':drop' => $drop->id, ':matcher' => $matcher->id));
                                $invest->droped = $drop->id;
                                $invest->matcher = $matcher->id;

                                // recalcular campos en cache
                                Invest::invested($project->id, 'users');

                            } else {
                                $this->critical('Error in Invest dropped by matcher', ['errors' => $errors, $matcher, 'drop' => $drop->id, 'drop_amount' => $drop_amount, 'drop_user' => $drop->user, $invest, $invest->getProject(), $invest->getUser()]);

                            }

                            $this->info('Invest dropped by matcher', [$matcher, 'drop' => $drop->id, 'drop_amount' => $drop_amount, 'drop_user' => $drop->user, $invest, $invest->getProject(), $invest->getUser()]);

                            // Feed this failed payment
                            // Admin Feed
                            $coin = Currency::getDefault('html');
                            $log  = new Feed();
                            $user = $drop->getUser();
                            $log->setTarget($project->id)
                                ->populate(
                                Text::sys('matcher-feed-invest-by', strtoupper($method::getId())),
                                '/admin/invests',
                                new FeedBody(null, null, 'matcher-feed-user-invest', [
                                        '%MESSAGE%' => $response->getMessage(),
                                        '%USER%'    => Feed::item('user', $user->name, $user->id),
                                        '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id),
                                        '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                        '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                                        '%METHOD%'  => strtoupper($method::getId())
                                   ])
                            )
                                ->doAdmin('money');

                            // Public Feed
                            $log_html = new FeedBody(null, null, 'matcher-feed-invest', [
                                    '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                    '%DROP%'    => Feed::item('drop', Text::get('matcher-drop'), '/service/resources'),
                                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                                    '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id)
                                ]);
                            if ($invest->anonymous) {
                                $log->populate('regular-anonymous',
                                    '/user/profile/anonymous',
                                    $log_html,
                                    1);
                            } else {
                                $log->populate($user->name,
                                    '/user/profile/'.$user->id,
                                    $log_html,
                                    $user->avatar->id);
                            }
                            $log->doPublic('community');

                        }
                        $this->notice("Matcher has invests", [$matcher, $invest, 'matcher_processor' => $matcher->processor]);
                    } catch(MatcherProcessorException $e) {
                        $this->notice("No invests for Matcher", [$matcher, $invest, 'matcher_processor' => $matcher->processor, 'reason' => $e->getMessage()]);
                    }
                }
            }
        }
	}

	public static function getSubscribedEvents() {
		return array(
            AppEvents::INVEST_SUCCEEDED => ['onInvestSuccess', -2] // low priority (after general processing)
		);
	}
}
