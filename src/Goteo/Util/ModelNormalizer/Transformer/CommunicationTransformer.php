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
use Goteo\Model\Image;
use Goteo\Model\Filter;
use Goteo\Library\Text;
use Goteo\Model\Mail;
use Goteo\Model\Template;

/**
 * Transform a Model
 */
class CommunicationTransformer extends AbstractTransformer {

    protected $keys = ['id', 'subject', 'template','type', 'langs'];

    function getSubject(){
        $subject = $this->model->subject;
        return $subject;
    }

    function getTemplate(){
        $template = ($this->model->template == Template::NEWSLETTER ) ? Text::get('admin-communications-newsletter'): Text::get('admin-communications-communication');
        return $template;
    }

    function getType(){
        $type = $this->model->type;
        return $type;
    }

    function getLang() {
        $langs = $this->model->getLangsAvailable();
        return explode(',', $langs);
        ;
    }

    function getFilter() {
        $filter = Filter::get($this->model->filter);
        return $filter->name;
    }

    public function getImage() {
        if ($this->model->template == Template::NEWSLETTER) {
            return ($this->model->header) ? $this->model->getImage()->getLink(64, 64, true) : '';
        }
        return '';

    }

    public function getSuccess() {
        $status = $this->model->getStatus();
        return '<span class="label label-percent" style="background-color:hsl(' . (120 * $status/100) . ',45%,50%);">' . $status . ' % </span>';
    }

    public function getActions() {

        if (!$this->model->isActive() && !$this->model->isSent()) {
            $ret['edit'] = '/admin/communication/edit/' . $this->model->id;
        }
        $ret['preview'] = '/admin/communication/preview/' . $this->model->id;
        $ret['clone'] = '/admin/communication/copy/' . $this->model->id;
        $ret['details'] = '/admin/communication/detail/' . $this->model->id;

        return $ret;
    }
    
}
