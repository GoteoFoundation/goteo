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

class ImpactDataTransformer extends AbstractTransformer {

    protected $keys = ['id', 'title', 'description'];

    public function getActions() {
        if(!$u = $this->getUser()) return [];

        $ret = [
            'edit' => '/admin/impactdata/edit/' . $this->model->id,
            'translate' => '/translate/impact_data/' . $this->model->id,
            'delete' => '/admin/impactdata/delete/' . $this->model->id
        ];

        return $ret;
    }
    
}
