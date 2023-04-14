<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Application\Message;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\UrlType;
use Symfony\Component\Form\FormInterface;

class PageForm extends AbstractFormProcessor
{
    public function createForm(): PageForm
    {
        $model = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('id', TextType::class, [
                'disabled' => (bool) $model->id,
            ])
            ->add('name', TextType::class, [])
            ->add('description', TextareaType::class, [])
            ->add('url', UrlType::class, [])
            ->add('content', MarkdownType::class, [])
            ->add('type', ChoiceType::class,[
                'choices' => $this->getTypes(),
            ])
            ->add('pending', BooleanType::class, [
                'label' => 'mark-pending',
                'attr' => [
                    'color' => 'cyan'
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): PageForm
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        if (!$model->id) {
            $model->rebuildData($data, array_keys($form->all()));
            $errors = [];
            if (!$model->add($errors)) {
                throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
            }
        } else {
            $model->rebuildData($data, array_keys($form->all()));
            $model->save($data);
            $errors = [];
            if (!$model->save($errors)) {
                throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
            }
        }

        if ($data['pending'] && !Page::setPending($model->id, 'page'))
            Message::error(Text::get('admin-page-pending-fail'));

        return $this;
    }

    private function getTypes(): array
    {
        return [
            'admin-text-type-html' => Page::TYPE_HTML,
            'admin-text-type-md' => Page::TYPE_MARKDOWN
        ];
    }
}
