<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\MatcherProcessor;

use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Payment\Method\PaymentMethodInterface;

interface MatcherProcessorInterface {

    /**
     * Checks if the processor handles a $matcher cammpaign
     * @return bool true | false
     */
    static public function is(Matcher $matcher);

    /**
     * Creates and instance of the processor for the matcher
     * @return bool MatcherProcessorInterface class or Exception
     */
    static public function create(Matcher $matcher);


    /**
     * Sets the matcher
     * @param Matcher $matcher [description]
     */
    public function setMatcher(Matcher $matcher);

    /**
     * Gets the matcher
     * @return Matcher
     */
    public function getMatcher();

    /**
     * Sets the context project
     * @param Project $project
     */
    public function setProject(Project $project);

    /**
     * Gets the context project
     * @return Project
     */
    public function getProject();

    /**
     * Sets the context invest
     * @param Invest $invest
     */
    public function setInvest(Invest $invest);

    /**
     * Gets the context invest
     * @return Invest
     */
    public function getInvest();

    /**
     * Sets the context payment method
     * @param PaymentMethodInterface $method
     */
    public function setMethod(PaymentMethodInterface $method);

    /**
     * Gets the context payment method
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * Returns a list of additional invests if matcher logic applies
     * @return array lists of invests
     */
    public function getInvests();

}
