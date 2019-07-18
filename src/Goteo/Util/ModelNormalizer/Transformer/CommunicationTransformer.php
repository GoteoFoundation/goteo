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
class CommunicationTransformer extends AbstractTransformer {

    protected $keys = ['id', 'subject', 'template','type', 'langs'];

    function getSubject(){
        $subject = $this->model->subject;
        return $subject;
    }

    function getTemplate(){
        $template = $this->model->template;
        return $template;
    }

    function getType(){
        $type = $this->model->type;
        return $type;
    }

    function getLang() {
        $lang = $this->model->getLangsAvailable();
        return $lang;
    }

    public function getActions() {
        $ret = [
            'preview' => '/admin/communication/preview/id/' . $this->model->id,
        ];

        return $ret;
    }
    
}
