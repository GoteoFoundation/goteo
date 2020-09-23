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
class ChannelPostsTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'order'];

    public function getRawValue($key) {
        if ($key == 'id') {
            return $this->model->post_id;
        }
        return $this->model->{$key};
    }

    public function getId() {
        return $this->model->post_id;
    }
    public function getActions() {
        if(!$u = $this->getUser()) return [];

        $ret = ['delete' => '/admin/channelposts/' . $this->model->node_id . '/delete/' . $this->model->post_id];

        return $ret;
    }

    public function getLink($type = 'public', $key = null) {

        if($key !== 'id') return '';
        if($type === 'public') {
            return '/post/' . $this->model->id .'?preview';
        }
        return '';
    }
}
