<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class FaqSectionTransformer extends AbstractTransformer
{
    protected $keys = ['id', 'name', 'slug', 'icon', 'order'];

    public function getSlug(): string
    {
        return $this->model->slug;
    }

    public function getActions(): array
    {
        if(!$this->getUser()) return [];

        return [
            'edit' => '/admin/faqsection/' . $this->model->id . '/edit',
            'delete' => '/admin/faqsection/' . $this->model->id . '/delete'
        ];
    }
}
