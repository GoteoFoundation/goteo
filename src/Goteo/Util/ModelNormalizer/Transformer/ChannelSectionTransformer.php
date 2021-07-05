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
class ChannelSectionTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'description'];

    public function getSection(){
        return $this->model->section;
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = [
            'edit' => '/admin/channelsection/'. $this->model->node . '/edit/' . $this->model->id,
            'delete' => '/admin/channelsection/'. $this->model->node . '/delete/' . $this->model->id
        ];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/node_section/' . $this->model->id;
        }

        return $ret;
    }
}
