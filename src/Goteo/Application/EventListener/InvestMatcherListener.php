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
use Goteo\Application\Currency;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Util\MatcherProcessor\MatcherProcessorException;
use Goteo\Util\MatcherProcessor\MatcherProcessorInterface;

//

class InvestMatcherListener extends AbstractMatcherListener {

    public function processPayments(Matcher $matcher, MatcherProcessorInterface $processor, Invest $invest = null) {
        try {
            $project = $processor->getProject();

            $invests = $processor->getInvests();

            $this->notice("Matcher has invests", [$matcher, 'matcher_processor' => $matcher->processor, $project]);

            foreach($invests as $drop) {
                $errors = [];
                $log = [$matcher, 'drop' => $drop->id, 'drop_amount' => $drop_amount, 'drop_user' => $drop->user];
                if($invest) {
                    $log[] = $invest;
                    $log[] = $invest->getProject();
                    $log[] = $invest->getUser();
                } else {
                    $log[] = $project;
                }
                // se actualiza el registro de convocatoria
                if ($drop->save($errors)) {
                    // recalcular campos en cache
                    Invest::invested($project->id, 'users');

                    $this->info('Invest dropped by matcher', $log);

                    // Feed this payment
                    // Admin Feed
                    $coin = Currency::getDefault('html');
                    $log  = new Feed();
                    $log->setTarget($project->id);
                    if($invest) {
                        $user = $invest->getUser();
                        $method = $invest->getMethod();
                        $log->populate(
                            Text::sys('matcher-feed-invest-by', strtoupper($method::getId())),
                            '/admin/invests',
                            new FeedBody(null, null, 'matcher-feed-user-invest', [
                                '%USER%'    => Feed::item('user', $user->name, $user->id),
                                '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id),
                                '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                                '%METHOD%'  => strtoupper($method::getId())
                           ])
                        );
                    } else {
                        $log->populate(
                            Text::sys('matcher-feed-invest-standalone'),
                            '/admin/invests',
                            new FeedBody(null, null, 'matcher-feed-standalone-invest', [
                                '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id),
                                '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                '%PROJECT%' => Feed::item('project', $project->name, $project->id)
                           ])
                        );
                    }
                    $user = $drop->getUser();
                    $log->doAdmin('money')
                        // Public Feed
                        ->populate($user->name,
                            '/user/profile/'.$user->id,
                            new FeedBody(null, null, 'matcher-feed-invest', [
                                '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                '%DROP%'    => Feed::item('drop', Text::get('matcher-drop'), '/service/resources'),
                                '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                                '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id)
                            ]),
                            $user->avatar->id
                        )
                        ->doPublic('community');

                } else {
                    $log['errors'] = $errors;
                    $this->critical('Error in Invest dropped by matcher', $log);
                    if($invest) {
                        $user = $invest->getUser();
                        $method = $invest->getMethod();
                        $log->populate(
                            Text::sys('matcher-feed-invest-by', strtoupper($method::getId())),
                            '/admin/invests',
                            new FeedBody(null, null, 'matcher-feed-user-invest-error', [
                                '%MESSAGE%' => implode(', ', $errors),
                                '%USER%'    => Feed::item('user', $user->name, $user->id),
                                '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id),
                                '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                                '%METHOD%'  => strtoupper($method::getId())
                           ])
                        );
                    } else {
                        $log->populate(
                            Text::sys('matcher-feed-invest-standalone'),
                            '/admin/invests',
                            new FeedBody(null, null, 'matcher-feed-standalone-invest-error', [
                                '%MESSAGE%' => implode(', ', $errors),
                                '%MATCHER%' => Feed::item('matcher', $matcher->name, $matcher->id),
                                '%AMOUNT%'  => Feed::item('money', $drop->amount.' '.$coin),
                                '%PROJECT%' => Feed::item('project', $project->name, $project->id)
                           ])
                        );
                    }

                }
            }
            // Update matcher data stats and pool amounts in matcher-users
            $matcher->save();

        } catch(MatcherProcessorException $e) {
            $this->notice("No invests for Matcher", [$matcher, 'matcher_processor' => $matcher->processor, $project, 'reason' => $e->getMessage()]);
        }

    }

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
                if(!$this->hasAppListener($matcher)) continue;

                // find processor, and execute it
                if($processor = $this->getService('app.matcher.finder')->getProcessor($matcher)) {
                    $processor->setProject($project);
                    $this->processPayments($matcher, $processor, $invest);

                    Invest::query("UPDATE invest SET droped = :drop, `matcher`= :matcher WHERE id = :id",
                            array(':id' => $invest->id, ':drop' => $drop->id, ':matcher' => $matcher->id));
                    $invest->droped = $drop->id;
                    $invest->matcher = $matcher->id;
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
