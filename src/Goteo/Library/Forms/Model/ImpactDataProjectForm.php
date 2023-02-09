<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class ImpactDataProjectForm extends AbstractFormProcessor {

    private function getImpactDataList(): array
    {
        $count = ImpactData::getList(['not_source' => 'manual'], 0, 0, true);
        $impactDataList = ImpactData::getList(['not_source' => 'manual'], 0, $count);

        $list = [];
        foreach($impactDataList as $impactData) {
            $list[$impactData->title] = $impactData->id;
        }

        return $list;

    }

    private function getImpactDataListByFootprint(Footprint $footprint): array
    {
        $count = ImpactData::getList(['not_source' => 'manual', 'footprint' => $footprint->id], 0, 0, true);
        $impactDataList = ImpactData::getList(['not_source' => 'manual', 'footprint' => $footprint->id], 0, $count);

        $list = [];
        foreach($impactDataList as $impactData) {
            $list[$impactData->title] = $impactData->id;
        }

        return $list;
    }

    public function createForm(): ImpactDataProjectForm
    {
        $builder = $this->getBuilder();
        $footprint = $this->getOption('footprint');

        $impactDataList = [];
        if ($footprint) {
            $impactDataList = $this->getImpactDataListByFootprint($footprint);
        } else {
            $impactDataList = $this->getImpactDataList();
        }

        $builder
            ->add('impact_data_id', ChoiceType::class, [
                'label' => Text::get('form-impact-data-project-impact-data-list'),
                'choices' => $impactDataList
            ])
            ->add('estimation_amount', NumberType::class, [
                'label' => Text::get('form-impact-data-project-estimation-amount'),
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('data', NumberType::class, [
                'label' => Text::get('form-impact-data-project-data'),
                'constraints' => [
                    new Constraints\NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class)
        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): ImpactDataProjectForm
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $impactData = ImpactData::get($data['impact_data_id']);
        $model = $this->getModel();
        $model
            ->setImpactData($impactData)
            ->setEstimationAmount($data['estimation_amount'])
            ->setData($data['data']);
        ;

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return $this;
    }
}
