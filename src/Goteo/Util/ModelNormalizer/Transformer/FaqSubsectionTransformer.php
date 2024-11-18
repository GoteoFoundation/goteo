<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Faq\FaqSection;

class FaqSubsectionTransformer extends AbstractTransformer
{
    protected $keys = ['id', 'name', 'section', 'order'];

    public function getSubsection(): string
    {
        try {
            $faqSection = FaqSection::getById($this->model->section_id);
        } catch (ModelNotFoundException $e) {
            return '';
        }

        return $faqSection->name;
    }

    public function getActions(): array
    {
        if(!$this->getUser()) return [];

        return [
            'edit' => '/admin/faqsubsection/' . $this->model->id . '/edit',
            'delete' => '/admin/faqsubsection/' . $this->model->id . '/delete'
        ];
    }
}
