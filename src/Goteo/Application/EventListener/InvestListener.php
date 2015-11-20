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
use Goteo\Application\Config;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestFinishEvent;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Library\Currency;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Mail;
use Goteo\Model\Template;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InvestListener extends AbstractListener {
	public function onInvestInit(FilterInvestInitEvent $event) {
		$invest  = $event->getInvest();
		$method  = $event->getMethod();
		$request = $event->getRequest();

        $method->setInvest($invest);
        $method->setRequest($request);

        // Is this really necessary?
        $this->info('Invest init', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, 'init', 'Invest input created');
	}

	public function onInvestInitRequest(FilterInvestRequestEvent $event) {
		$method = $event->getMethod();
		$invest = $method->getInvest();
		$this->info('Invest init request', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);

		Invest::setDetail($invest->id, 'init-request', 'Payment gateway authorised');
	}

	public function onInvestInitRedirect(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$response = $event->getResponse();
		$invest   = $method->getInvest();
		$reward   = $invest->getFirstReward();

		$this->info('Invest init redirect', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, 'init-redirect', 'Redirecting to payment gateway');

		// Goto payment platform...

		// Assign response if not previously assigned
		if (!$event->getHttpResponse()) {
			$event->setHttpResponse($response->getRedirectResponse());
		}
	}

	public function onInvestComplete(FilterInvestInitEvent $event) {
		$invest  = $event->getInvest();
		$method  = $event->getMethod();
		$request = $event->getRequest();

        $method->setInvest($invest);
        $method->setRequest($request);

        $this->info('Invest complete', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, 'complete', 'Redirected from payment gateway');
	}

	public function onInvestCompleteRequest(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$invest   = $method->getInvest();
		$response = $event->getResponse();

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

		$this->info('Invest complete request', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, 'complete-request', 'Redirecting to user data');
	}

	public function onInvestNotify(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$invest   = $method->getInvest();
		$response = $event->getResponse();

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

		$this->info('Invest notify', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, 'notify', 'Contact from payment gateway');
	}

	public function onInvestFailed(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$response = $event->getResponse();
		$invest   = $method->getInvest();
		$reward   = $invest->getFirstReward();

		$this->warning('Invest finish failed', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), 'message' => $response->getMessage()]);

		// not making changes on invest status...

		// Feed this failed payment
		// Admin Feed
		$coin    = Currency::getDefault()['html'];
		$log     = new Feed();
		$project = $invest->getProject();
		$user    = $invest->getUser();
		$log->setTarget($project->id)
			->populate(
			Text::sys('feed-invest-by', strtoupper($method::getId())),
			'/admin/invests',
			new FeedBody(null, null, 'feed-user-invest-error', [
					'%MESSAGE%' => $response->getMessage(),
					'%USER%'    => Feed::item('user', $user->name, $user->id),
					'%AMOUNT%'  => Feed::item('money', $invest->amount.' '.$coin),
					'%PROJECT%' => Feed::item('project', $project->name, $project->id),
					'%METHOD%'  => strtoupper($method::getId())
				])
		)
			->doAdmin('money');

		Invest::setDetail($invest->id, 'confirm-fail', 'Invest process failed. Gateway error: '.$response->getMessage());

		// Assign response if not previously assigned
		// Goto user start
		if (!$event->getHttpResponse()) {
			$event->setHttpResponse(new RedirectResponse('/invest/'.$invest->project.'/payment?'.http_build_query(['amount' => $invest->amount_original.$invest->currency, 'reward' => $reward?$reward->id:'0'])));
		}

	}

	public function onInvestSuccess(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$response = $event->getResponse();
		$invest   = $method->getInvest();
		$project  = $invest->getProject();
		$user     = $invest->getUser();

		$this->notice('Invest finish succeeded', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);

		// Invest status to charged
		$invest->status = Invest::STATUS_CHARGED;
		// Set charged date if empty
		if (empty($invest->charged)) {
			$invest->charged = date('Y-m-d');
		}
		$errors = [];
		$invest->save($errors);
		if ($errors) {
			throw new \RuntimeException('Error saving Invest details! '.implode("\n", $errors));
		}

		// MAIL SENDING TO DONOR
		// Thanks template
		$original_lang = $lang = Lang::current();
        // die("$original_lang");
		$template = Template::get(Template::DONOR_INVEST_THANKS, $lang);// lang will be updated to the available template lang
        if($original_lang != $lang) {
            $this->warning('Template lang changed', [$template, 'old_lang' => $original_lang, 'new_lang' => $lang, $invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser() ]);
        }
		// Reward or resign text
		if ($invest->resign) {
			$txt_rewards = Text::lang('invest-template-resign', $lang);
		} else {
			$txt_rewards = Text::lang('invest-template-reward', $lang, ['%REWARDS%' => '<strong>'.$invest->getFirstReward()->reward.'</strong>']);
		}

		// method:
		if ($invest->method == 'pool') {
			$txt_method = Text::lang('invest-template-with-pool', $lang, ['%AMOUNT%' => \amount_format($invest->amount)]);
		} elseif ($invest->pool) {
			// aporte reservando al monedero
			$txt_method = Text::lang('invest-template-to-pool', $lang, ['%AMOUNT%' => \amount_format($invest->amount)]);
		} elseif ($project->round == 2) {
			// si aporte en segunda ronda
			$txt_method = Text::lang('invest-template-round-two', $lang, ['%AMOUNT%' => $invest->amount]);
		} else {
			// resto de casos
			$txt_method = Text::lang('invest-template-round-one', $lang, ['%AMOUNT%' => $invest->amount]);
		}

		// Dirección en el mail (y version para regalo)
		$txt_address = Text::get('invest-address-address-field').' '.$invest->address->address;
		$txt_address .= '<br> '.Text::get('invest-address-zipcode-field').' '.$invest->address->zipcode;
		$txt_address .= '<br> '.Text::get('invest-address-location-field').' '.$invest->address->location;
		$txt_address .= '<br> '.Text::get('invest-address-country-field').' '.$invest->address->country;

		$txt_destaddr = $txt_address;
		$txt_address  = Text::get('invest-mail_info-address').'<br>'.$txt_address;

		// Sustituimos los datos
		$subject = str_replace('%PROJECTNAME%', $project->name, $template->title);

		$txt_droped = '';

		// datos para el drop
		if (!empty($invest->droped) && class_exists('\Goteo\Model\Call')) {
			$drop = Invest::get($invest->droped);
			$call = \Goteo\Model\Call::getMini($drop->call);
			// texto de capital riego
			$txt_droped = Text::get('invest-mail_info-drop', $call->user->name, \amount_format($drop->amount), $call->name);
		}

		// En el contenido:
		$search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%', '%DROPED%', '%METHOD%');
		$replace = array($user->name, $project->name, Config::getUrl($lang).'/project/'.$project->id, $invest->amount, $txt_rewards, $txt_address, $txt_droped, $txt_method);
		$content = str_replace($search, $replace, $template->text);

		$mailHandler            = new Mail();
		$mailHandler->lang      = $lang;
		$mailHandler->reply     = Config::getMail('contact');


		$mailHandler->replyName = Config::get('mail.transport.name');
		$mailHandler->to        = $user->email;
		$mailHandler->toName    = $user->name;
		$mailHandler->subject   = $subject;
		$mailHandler->content   = $content;
		$mailHandler->html      = true;
		$mailHandler->template  = $template->id;
		$errors                 = [];
		if ($mailHandler->send($errors)) {
			Message::info(Text::get('project-invest-thanks_mail-success'));
            $this->notice('Invest user mail sent', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), $mailHandler]);
        } else {
            Message::error(Text::get('project-invest-thanks_mail-fail'));
            Message::error(implode('<br />', $errors));
            $this->warning('Invest user mail error', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), $mailHandler, 'errors' => $errors]);
		}
		unset($mailHandler);

		//         // si es un regalo
		// if ($invest->address->regalo && !empty($invest->address->emaildest)) {
		//     // Notificación al destinatario de regalo
		//     $template = Template::get(Template::BAZAAR_RECEIVER, $comlang);
		//     // Sustituimos los datos
		//     $subject = str_replace('%USERNAME%', $user->name, $template->title);

		//     // En el contenido:
		//     $search  = array('%DESTNAME%', '%USERNAME%', '%MESSAGE%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%PROJAMOUNT%', '%PROJPER%', '%REWNAME%', '%ADDRESS%', '%DROPED%');
		//     $replace = array($invest->address->namedest, $user->name, $invest->address->message, $projectData->name, $URL.'/project/'.$projectData->id, $invest->amount, $amount, $percent, $txt_rewards, $txt_destaddr, $txt_droped);
		//     $content = \str_replace($search, $replace, $template->text);

		//     $mailHandler = new Mail();
		//     $mailHandler->lang = $comlang;lang
		//     $mailHandler->to = $invest->address->emaildest;
		//     $mailHandler->toName = $invest->address->namedest;
		//     $mailHandler->subject = $subject;
		//     $mailHandler->content = $content;
		//     $mailHandler->html = true;
		//     $mailHandler->template = $template->id;
		//     if ($mailHandler->send($errors)) {
		//         Message::info(Text::get('project-invest-friend_mail-success'));
		//     } else {
		//         Message::error(Text::get('project-invest-friend_mail-fail'));
		//         Message::error(implode('<br />', $errors));
		//     }

		//     unset($mailHandler);
		// }

		// MAIL SENDING TO AUTHOR
		//  idioma de preferencia
		$original_lang = $lang = User::getPreferences($project->getOwner())->comlang;

		$template = Template::get(Template::OWNER_NEW_INVEST, $lang); // Lang will be updated to the found template lang
        if($original_lang != $lang) {
            $this->warning('Template lang changed', [$template, 'old_lang' => $original_lang, 'new_lang' => $lang, $invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser() ]);
        }
		// Sustituimos los datos
		$subject = str_replace('%PROJECTNAME%', $project->name, $template->title);

		// En el contenido:
		$search  = array('%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%AMOUNT%', '%MESSAGEURL%', '%DROPED%');
		$replace = array($project->user->name, $user->name, $project->name, $URL, $invest->amount, Config::getUrl().'/user/profile/'.$user->id.'/message', $txt_droped);
		$content = str_replace($search, $replace, $template->text);

		$mailHandler           = new Mail();
		$mailHandler->lang     = $lang;
		$mailHandler->to       = $project->user->email;
		$mailHandler->toName   = $project->user->name;
		$mailHandler->subject  = $subject;
		$mailHandler->content  = $content;
		$mailHandler->html     = true;
		$mailHandler->template = $template->id;
        $errors = [];
		if($mailHandler->send($errors)) {
            $this->notice('Invest owner mail sent', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), $mailHandler]);
        } else {
            $this->warning('Invest owner mail error', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), 'method' => $method::getId(), $mailHandler, 'errors' => $errors]);
        }

		// Feed this succeeded payment
		// Admin Feed
		$coin    = Currency::getDefault()['html'];
		$log     = new Feed();
		$project = $invest->getProject();
		$user    = $invest->getUser();
		$log->setTarget($project->id)
			->populate(
			Text::sys('feed-invest-by', strtoupper($method::getId())),
			'/admin/invests',
			new FeedBody(null, null, 'feed-user-invest', [
					'%USER%'    => Feed::item('user', $user->name, $user->id),
					'%AMOUNT%'  => Feed::item('money', $invest->amount.' '.$coin),
					'%PROJECT%' => Feed::item('project', $project->name, $project->id),
					'%METHOD%'  => strtoupper($method::getId())
				])
		)
			->doAdmin('money');

		// Public Feed
		$log_html = new FeedBody(null, null, 'feed-invest', [
				'%AMOUNT%'  => Feed::item('money', $invest->amount.' '.$coin),
				'%PROJECT%' => Feed::item('project', $project->name, $project->id)
			]);
		if ($invest->anonymous) {
			$log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
		} else {
			$log->populate($user->name, '/user/profile/'.$user->id, $log_html, $user->avatar->id);
		}
		$log->doPublic('community');

		// update cached data
		$invest->keepUpdated();

		Invest::setDetail($invest->id, 'confirmed', 'Invest process completed successfully');

		// Assign response if not previously assigned
		if (!$event->getHttpResponse()) {
			$event->setHttpResponse(new RedirectResponse('/invest/'.$invest->project.'/'.$invest->id));
		}
	}

	/**
	 * Cancels and invest for other reasons than failed projects
	 * @param  FilterInvestRefundEvent $event
	 */
	public function onInvestRefundCancel(FilterInvestRefundEvent $event) {
		$method = $event->getMethod();
		$invest = $event->getInvest();
        if ($invest->cancel(false)) {
            $this->notice('Invest cancelled', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
            Invest::setDetail($invest->id, $method::getId().'-cancel', 'Invest process manually cancelled successfully');
            // update cached data
            $invest->keepUpdated();
        } else {
            $this->warning('Error cancelling invest', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
            Invest::setDetail($invest->id, $method::getId().'-cancel-fail', 'Error while cancelling invest');
        }

    }

    /**
     * Cancels and invest for failed projects
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestRefundReturn(FilterInvestRefundEvent $event) {
        $method = $event->getMethod();
        $invest = $event->getInvest();
        $this->notice('Invest refund cancel', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
        if ($invest->cancel(true)) {
            $this->notice('Invest refund succeeded', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
            Invest::setDetail($invest->id, $method::getId().'-cancel', 'Invest refunded successfully');
            // update cached data
            $invest->keepUpdated();
        } else {
		    $this->warning('Error refunding invest', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
            Invest::setDetail($invest->id, $method::getId().'-cancel-fail', 'Error while refunding invest');
		}

	}
    /**
     * Handles failed refund process
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestRefundFailed(FilterInvestRefundEvent $event) {
        $method   = $event->getMethod();
        $invest   = $event->getInvest();
        $response = $event->getResponse();
        $this->warning('Invest refund failed', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser(), 'messages' => $response->getMessage()]);
        Invest::setDetail($invest->id, $method::getId().'-return-fail', 'Error while refunding invest: '.$response->getMessage());

    }

    /**
     * Once user has entered data
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestFinished(FilterInvestFinishEvent $event) {
        $request = $event->getRequest();
        $invest  = $event->getInvest();

		$this->notice('Invest finished', [$invest, $invest->getProject(), $invest->getFirstReward(), $invest->getUser()]);
		Invest::setDetail($invest->id, $invest->method.'-return-data', 'User has saved personal data for rewards');

	}

	/**
	 * Response should not be manipulated for controller Invest and method notifiy
	 * @param  FilterResponseEvent $event [description]
	 * @return [type]                     [description]
	 */
	public function onKernelResponse(FilterResponseEvent $event) {

		$request = $event->getRequest();

		if ($request->attributes->get('_controller') == 'Goteo\Controller\InvestController::notifyPaymentAction') {
			$event->stopPropagation();
		}

	}

	public static function getSubscribedEvents() {
		return array(
			AppEvents::INVEST_INIT             => 'onInvestInit',
			AppEvents::INVEST_INIT_REQUEST     => 'onInvestInitRequest',
			AppEvents::INVEST_INIT_REDIRECT    => 'onInvestInitRedirect',
			AppEvents::INVEST_COMPLETE         => 'onInvestComplete',
			AppEvents::INVEST_COMPLETE_REQUEST => 'onInvestCompleteRequest',
			AppEvents::INVEST_NOTIFY           => 'onInvestNotify',
			AppEvents::INVEST_FAILED           => 'onInvestFailed',
			AppEvents::INVEST_SUCCEEDED        => 'onInvestSuccess',
			AppEvents::INVEST_CANCELLED        => 'onInvestRefundCancel',
			AppEvents::INVEST_CANCEL_FAILED    => 'onInvestRefundFailed', // same action as return at this moment
			AppEvents::INVEST_RETURNED         => 'onInvestRefundReturn',
            AppEvents::INVEST_RETURN_FAILED    => 'onInvestRefundFailed',
			AppEvents::INVEST_FINISHED         => 'onInvestFinished',
			KernelEvents::RESPONSE             => array('onKernelResponse', 100),
		);
	}
}
