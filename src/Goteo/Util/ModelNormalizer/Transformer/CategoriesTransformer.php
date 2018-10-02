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
class CategoriesTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title'];

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/categories/' . $this->getModelName() . '/edit/' . $this->model->id];

        if($this->getUser()->hasPerm('translate-language')) {
            $ret['translate'] = '/translate/' . $this->model->getTable() . '/' . $this->model->id;
        }

        // $ret['preview'] = '/categories/' . $this->model->id . '?preview';
        return $ret;
    }

    public function getApiProperty($prop) {
        return '/api/categories/' . $this->getModelName(). '/' . $this->model->id . "/property/$prop";
    }

    public function getSocial_commitment() {
        return $this->model->getSocialCommitment() ? $this->model->getSocialCommitment()->name : '';
    }

    public function getSdgs() {
        return $this->model->getSdgs();
    }

    public function getFootprints() {
        return $this->model->getFootprints();
    }

    public function getCategories() {
        return $this->model->getCategories();
    }

    public function getSocial_commitments() {
        return $this->model->getSocialCommitments();
    }

    public function getLanding_match() {
        return $this->model->landing_match;
    }

    public function getLink($type = 'public', $key = null) {
        if($key !== 'id') return '';
        return '';
    }
}
