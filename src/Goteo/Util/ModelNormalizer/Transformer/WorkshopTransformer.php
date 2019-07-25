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
class WorkshopTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'subtitle'];

    public function getInfo() {
        return '<strong>'.$this->getTitle().'</strong>';
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/workshop/edit/' . $this->model->id];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/' . $this->getModelName() . '/' . $this->model->id;
        }

        $ret['preview'] = '/workshop/' . $this->model->id;

        return $ret;
    }

}
