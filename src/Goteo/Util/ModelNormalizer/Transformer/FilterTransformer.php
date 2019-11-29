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

    public function getLabel($key) {
        if ($key == "users") {
            return "<i class='fa fa-users'></i> " . Text::get("admin-title-$key");
        }
        return Text::get("admin-title-$key");
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
        $receivers = $this->model->getFiltered(0, 0, true);
        return $receivers;
    }

    function getRole() {
        $role = $this->model->role;
        return Text::get("admin-filter-" . $role);
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = [
            'edit' => '/admin/filter/edit/' . $this->model->id,
        ];

        if (!$this->model->isUsed())
            $ret['delete'] = '/admin/filter/delete/'. $this->model->id;

        return $ret;
    }
    
}
