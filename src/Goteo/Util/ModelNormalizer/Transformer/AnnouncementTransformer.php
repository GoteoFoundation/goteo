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

use Goteo\Library\Text;
use Goteo\Model\Announcement;

class AnnouncementTransformer extends AbstractTransformer
{

    protected $keys = ['id', 'title', 'description', 'type'];

    public function getType(): string
    {
        /** @var Announcement */
        $model = $this->model;
        $type = $model->type;
        return Text::get("admin-announcements-type-$type");
    }

    public function getActions()
    {
        /** @var Announcement */
        $model = $this->model;

        if (!$this->getUser()) return [];

        $id = $model->id;
        $ret = [
            'edit' => "/admin/announcement/edit/$id",
            'translate' => "/translate/announcement/$id",
            'delete' => "/admin/announcement/delete/$id",
        ];

        return $ret;
    }
}
