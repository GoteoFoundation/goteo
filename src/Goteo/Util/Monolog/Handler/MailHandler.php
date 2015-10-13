<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Monolog\Handler;

use Monolog;

/**
 * MailHandler uses Mail to send the emails
 *
 * @author Ivan Vergés
 */
class MailHandler extends Monolog\Handler\MailHandler
{
    protected $mailer;

    /**
     * @param \Mail           $mailer  The mailer to use
     * @param integer                 $level   The minimum logging level at which this handler will be triggered
     * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\Goteo\Model\Mail $mailer, $level = Monolog\Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $this->mailer->content = '<pre>' . $content . '</pre>' . $this->mailer->content;
        $this->mailer->send();
    }
}
