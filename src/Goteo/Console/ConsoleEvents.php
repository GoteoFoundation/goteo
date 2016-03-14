<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console;

final class ConsoleEvents
{
    /**
     * The console.refund.cancel event is thrown each time a payments processes a refund
     * for any reason others than the project related is not archived/failed
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCELLED = 'console.refund.cancel';

    /**
     * The console.refund.cancel.failed event is thrown when manual refund it's done (normally a admin call)
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCEL_FAILED = 'console.refund.cancel.failed';

    /**
     * The console.refund.return event is thrown each time a payments processes a refund
     * due the project related is archived/failed
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURNED = 'console.refund.return';

    /**
     * The console.refund.cancel event is thrown a refund process
     * due the project related is archived/failed fails
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURN_FAILED = 'console.refund.failed';

    /**
     * The console.project.publish event is thrown when a project is in a REVIEW status and has to be published this day
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_PUBLISH = 'console.project.publish';

    /**
     * The console.project.ending event is thrown when a project is about to end his active campaign
     * triggered when 5, 3, 2 or 1 days left
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_ENDING = 'console.project.ending';

    /**
     * The console.project.active event is thrown once a day whilst active campaign
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_ACTIVE = 'console.project.active';

    /**
     * The console.project.watch event is thrown once a day until a project has reached the status "fullfilled" (one year time max)
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_WATCH = 'console.project.watch';

    /**
     * The console.project.fail event is thrown when a project reaches the end of his active campaign
     * and fails to achieve the amount required
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_FAILED = 'console.project.fail';

    /**
     * The console.project.one_round event is thrown when a project reaches the end of the
     * 1st round (and unique) of its campaign and successfully achieves the amount required
     * Only for projects configured as one-round-only
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_ONE_ROUND = 'console.project.one_round';

    /**
     * The console.project.round1 event is thrown when a project reaches the end of the
     * 1st round (and it's not unique) of its campaign and successfully achieves the amount required
     * Only for projects configured as 2-rounds
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_ROUND1 = 'console.project.round1';

    /**
     * The console.project.round2 event is thrown when a project reaches the end of the
     * 2nd round of its campaign (which means it's already successful)
     * Only for projects configured as 2-rounds
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_ROUND2 = 'console.project.round2';

    /**
     * The console.mailing.sendmail event is thrown when a
     * individual mail from the mailer_send table is sent
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterSendmailEvent instance.
     *
     * @var string
     */
    const MAILING_SENDMAIL = 'console.mailing.sendmail';

    /**
     * The console.mailing.start event is thrown when a
     * massive mailing starts
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterSendmailEvent instance.
     *
     * @var string
     */
    const MAILING_STARTED = 'console.mailing.start';

    /**
     * The console.mailing.finish event is thrown when a
     * massive mailing finishes
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterMailingEvent instance.
     *
     * @var string
     */
    const MAILING_FINISHED = 'console.mailing.finish';

    /**
     * The console.mailing.abort event is thrown when a
     * massive mailing is aborted due any reaason
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterMailingEvent instance.
     *
     * @var string
     */
    const MAILING_ABORTED = 'console.mailing.abort';

}
