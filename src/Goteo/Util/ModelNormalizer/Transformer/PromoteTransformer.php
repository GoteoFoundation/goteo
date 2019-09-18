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
class PromoteTransformer extends AbstractTransformer {

    protected $keys = ['id', 'image', 'title', 'subtitle'];

    public function getInfo() {
        $prj = $this->model->getProject();
        return '<strong>'.($prj ? $prj->name.' - ' : '') . $this->getAuthor() . '</strong><br>' . Text::recorta($this->getReview(), 30);
    }

    function getProject() {
        return $this->model->getProject();
    }

    function getStatus() {
        $status = $this->model->getStatus();
        return $status;
    }


    public function getActions() {
        $ret = ['delete' => '/admin/promote/delete/channel/'. $this->model->node . '/id/'. $this->model->id];
        return $ret;
    }
    
}
