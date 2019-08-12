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
class FilterTransformer extends AbstractTransformer {

    protected $keys = ['id', 'name', 'description'];

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

    function getDescription(){
        $description = $this->model->description;
        return $description;
    }

    function getUsers(){
        $receivers = $this->model->getFiltered(true);
        return $receivers;
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = [
            'edit' => '/admin/filter/edit/' . $this->model->id
        ];

        return $ret;
    }
    
}
