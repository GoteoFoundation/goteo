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
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCELLED = 'console.refund.cancel';

    /**
     * The console.refund.cancel.failed event is thrown when manual refund it's done (normally a admin call)
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCEL_FAILED = 'console.refund.cancel.failed';

    /**
     * The console.refund.return event is thrown each time a payments processes a refund
     * due the project related is archived/failed
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURNED = 'console.refund.return';

    /**
     * The console.refund.cancel event is thrown a refund process
     * due the project related is archived/failed fails
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURN_FAILED = 'console.refund.failed';

}
