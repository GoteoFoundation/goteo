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
class StoriesTransformer extends AbstractTransformer {

    protected $keys = ['id', 'image', 'title', 'subtitle'];

    public function getInfo() {
        $prj = $this->model->getProject();
        return '<strong>'.($prj ? $prj->name.' - ' : '') . $this->getAuthor() . '</strong><br>' . Text::recorta($this->getReview(), 30);
    }

    public function getReview() {
        return $this->model->review;
    }
    public function getAuthor() {
        return $this->model->getUser() ? $this->model->getUser()->name : ('<span class="label label-info">' . Text::get('admin-no-project') . '</span>');
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/stories/edit/' . $this->model->id];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/' . $this->getModelName() . '/' . $this->model->id;
        }

        // $ret['preview'] = '/stories/' . $this->model->id . '?preview';
        return $ret;
    }

    public function getLink($type = 'public', $key = null) {
        if($key !== 'id') return '';
        if($type === 'public') {
            return '/stories/' . $this->model->id .'?preview';
        }
        return '';
    }
}
