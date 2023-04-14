<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class PageTransformer extends AbstractTransformer
{
    protected $keys = ['id', 'name', 'type'];

    public function getType()
    {
        return $this->model->type;
    }

    public function getActions(): array
    {
        return [
            'edit' => '/admin/pages/edit/' . $this->model->id,
            'delete' => '/admin/pages/delete/' . $this->model->id,
            'preview' => $this->model->url,
            'translate' => '/translate/page/edit/' . $this->model->id,
        ];
    }
}
