<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

class ImpactItemProjectForm extends AbstractFormProcessor
{
    private function getImpactItems(): array
    {
        $impactItems = ImpactItem::getAll();
        $list = [];
        foreach($impactItems as $impactItem) {
            $list[$impactItem->getName()] = $impactItem->getId();
        }

        return $list;
    }

    public function createForm(): ImpactItemProjectForm
    {
        $model = $this->getModel();

        $builder = $this->getBuilder();
        $builder
            ->add("impact_item_id", ChoiceType::class, [
                'label' => "Impact item",
                'choices' => $this->getImpactItems(),
                'data' => $model->getImpactItem()->getId()
            ])
            ->add("value", NumberType::class, [
                "label" => 'regular-value',
                "data" => $model->getValue(),
            ])
            ->add("submit", SubmitType::class, [])

        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): ImpactItemProjectForm
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        $impactItem = ImpactItem::get($data['impact_item_id']);
        $model->setValue($data['value']);
        $model->setImpactItem($impactItem);

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',', $errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
