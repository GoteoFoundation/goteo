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
class ChannelResourceTransformer extends AbstractTransformer {

    protected $keys = ['id', 'image', 'title'];

    
    public function getInfo() {
        return '<strong>'.$this->getTitle().'</strong>';
    }
    
    public function getImage() {
        if ($this->model->image)
            return $this->model->getImage()->getLink(64,64,true);
        
        return $this->model->image;
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/channelresource/edit/' . $this->model->id];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/node_resource/' . $this->model->id;
        }

        return $ret;
    }

}
