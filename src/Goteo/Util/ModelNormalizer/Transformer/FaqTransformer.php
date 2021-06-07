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
use Goteo\Model\Faq\FaqSubsection;
use Goteo\Library\Text;

/**
 * Transform a Model
 */
class FaqTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'subsection_id','order'];


    public function getSection() {
        return $this->model->section;
    }

    public function getSubsection() {
        $subsection=FaqSubsection::get($this->model->subsection);
        return $subsection->name;
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = [
            'edit' => '/admin/faq/edit/' . $this->model->id,
            'delete' => '/admin/faq/delete/' . $this->model->id
        ];

        return $ret;
    }
    
}
