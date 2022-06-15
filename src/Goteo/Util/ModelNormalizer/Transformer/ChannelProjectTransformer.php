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


use Goteo\Model\Image;

/**
 * Transform a Model
 */
class ChannelProjectTransformer extends AbstractTransformer {

    protected $keys = ['id', 'image', 'name'];

    public function getActions(): array
    {
        if(!$this->getUser()) return [];

        return [
            'delete' => '/admin/channelprojects/'. $this->model->node_id . '/delete/' . $this->model->project_id
        ];
    }
}
