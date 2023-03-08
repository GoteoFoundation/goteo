<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Project\Cost;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;

class ImpactItemProjectCostForm extends AbstractFormProcessor
{

    private function getProjectCosts(): array
    {
        $model = $this->model;
        $project = $model->getImpactProjectItem()->getProject();
        $projectCosts = Cost::getAll($project);

        $list = [];
        foreach($projectCosts as $projectCost) {
            $list[$projectCost->cost] = $projectCost->id;
        }

        return $list;
    }
    public function createForm()
    {
        $model = $this->getModel();

        $cost_id = $model->getCost() ?? $model->getCost()->id;

        $builder = $this->getBuilder();
        $builder
            ->add('cost_id', ChoiceType::class, [
                'label' => 'Costs',
                'choices' => $this->getProjectCosts(),
                'data' => $cost_id
            ])
            ->add("submit", SubmitType::class, [])
        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        $cost = Cost::get($data["cost_id"]);
        $model->setCost($cost);
        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',', $errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
