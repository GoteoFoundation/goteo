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

/**
 * Transform a Model
 */
class PostTransformer extends AbstractTransformer {

    protected $keys = ['id', 'image', 'title', 'subtitle'];


    /**
     * Return an API url to modifiy the property (or empty if doesn't exist)
     * @param  [type] $prop [description]
     * @return [type]       [description]
     */
    public function getApiProperty($prop) {
        return '/api/blog/' . $this->getSlug() . "/property/$prop";
    }

    public function getPublish() {
        return $this->model->publish;
    }

    public function getSlug() {
        return $this->model->getSlug();
    }

    public function getInfo() {
        return '<strong>'.$this->getDate().' - ' . $this->getAuthor() . '</strong><br>'.$this->getSubTitle();
    }

    public function getAuthor() {
        return $this->model->getAuthor() ? $this->model->getAuthor()->name : 'Unknown';
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/blog/edit/' . $this->model->getSlug()];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/' . $this->getModelName() . '/' . $this->model->id;
        }

        $ret['preview'] = '/blog/' . $this->model->getSlug() . '?preview';
        return $ret;
    }

    public function getLink($type = 'public', $key = null) {
        if($key !== 'id') return '';
        if($type === 'public') {
            return '/blog/' . $this->model->getSlug() .'?preview';
        }
        return '';
    }
}
