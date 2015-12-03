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
use Monolog\Handler\MailHandler as MonologMailHandler;

use Goteo\Model\Mail;

/**
 * MailHandler uses Mail to send the emails
 *
 * @author Ivan Vergés
 */
class MailHandler extends MonologMailHandler
{
    protected $mailer;
    protected $delayed;
    private $messageTemplate;

    /**
     * @param \Mail           $mailer  The mailer to use
     * @param string or callable      $message   Initial message
     * @param integer                 $level   The minimum logging level at which this handler will be triggered
     * @param Boolean                 $delayed Whether the messages has to be send automatically or not. If not, sendDelayed() should be call in order to send
     * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(Mail $mailer, $message = '', $level = Monolog\Logger::ERROR, $delayed=false, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->mailer = $mailer;
        $this->delayed = $delayed;
        $this->messageTemplate = $message;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $this->mailer->content .= $this->buildMessage($content, $records);
        if(!$this->delayed) {
            $this->doSending();
        }
    }

    public function setDelayed($delayed) {
        return $this->delayed = (bool) $delayed;
        return $this;
    }

    public function isDelayed() {
        return $this->delayed;
    }

    public function sendDelayed() {
        if($this->delayed && $this->mailer->content) {
            $this->doSending();
            $this->delayed = false;
        }
    }

    protected function doSending() {

        $extra = \global_formatter([]);
        if($extra && $extra['extra']) {
            $this->mailer->content .= '<hr><pre style="font-size:0.8em;background:#f0f0f0">' . print_r($extra['extra'], true) .'</pre>';
        }
        $errors = [];
        if(!$this->mailer->send($errors)) {
            throw new \RuntimeException('Error sending delayed email: ' . implode("\n", $errors));
        }
        $this->mailer->id=null;
    }
    /**
     * Creates instance of Swift_Message to be sent
     *
     * @param string $content formatted email body to be sent
     * @param array  $records Log records that formed the content
     * @return \Swift_Message
     */
    protected function buildMessage($content, array $records)
    {
        $message = null;
        if (is_callable($this->messageTemplate)) {
            $message = call_user_func($this->messageTemplate, $content, $records);
        }
        else {
            $message = $content;
        }

        return $message;
    }
}
