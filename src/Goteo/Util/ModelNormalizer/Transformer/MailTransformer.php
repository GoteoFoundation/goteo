<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Util\ModelNormalizer\Transformer;

use Goteo\Core\Model;
use Goteo\Library\Text;

/**
 * Transform a Model
 */
class MailTransformer extends AbstractTransformer {

    protected $keys = ['id', 'name', 'description'];

    function getStatus() {
        $status = $this->model->getStatus();
        return $status;
    }

    function getSubject(){
        $subject = $this->model->subject;
        return $subject;
    }

    function getPercent(){
        $percent = $this->model->getStatusObject()->percent;
        $percent = '<span class="label label-percent" style="background-color:hsl(' . (120 * $percent/100) . ',45%,50%);">' . (int) $percent . ' % </span>';
        return $percent;
    }

    function getLang() {
        $lang = $this->model->lang;
        return $lang;
    }

    function getReceivers() {
        return $this->model->getStatusObject()->receivers;
    }

    function getSent() {
        return $this->model->getStatusObject()->sent;
    }

    function getFailed() {
        return $this->model->getStatusObject()->failed;
    }
    

    function getPending() {
        $pending = $this->model->getStatusObject()->pending;
        return $pending;
    }

    function getSuccess() {
        $success = (int) $this->model->getStats()->getEmailOpenedCollector()->getPercent();
        return '<span class="label label-percent" style="background-color:hsl(' . (120 * $success/100) . ',45%,50%);">' . $success . ' % </span>';
    }

    function getLink($type = 'public', $key = null) {

        if($key == 'subject') return '/admin/sent/detail/' . $this->model->id;
        return '';
    }
    
}
