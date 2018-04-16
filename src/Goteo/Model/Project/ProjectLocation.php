<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Project;

use Goteo\Model\Project;

class ProjectLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'project_location';
    protected static $Table_static = 'project_location';
    public $project;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->project = $this->id;
    }

    public static function get($project) {
        $id = $project;
        if($project instanceOf Project) {
            $id = $project->id;
        }
        return parent::get($id);
    }
}

