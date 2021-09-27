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
class ChannelProgramTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'description'];

    public function getDescription(){
        return $this->model->description;
    }

    public function getImage() {
        if ($this->model->header)
            return $this->model->getHeader()->getLink(64,64);
        
        return null;
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/channelprogram/'. $this->model->node_id . '/edit/' . $this->model->id];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/node_program/' . $this->model->id;
        }

        // $ret['preview'] = '/program/' . $this->model->id . '?preview';
        return $ret;
    }
}
