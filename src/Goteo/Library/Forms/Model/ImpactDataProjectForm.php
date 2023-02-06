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

use Goteo\Entity\ImpactData\ImpactDataProject;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Repository\ImpactDataProjectRepository;
use Goteo\Util\Form\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class ImpactDataProjectForm extends AbstractFormProcessor {

    public function createForm(): ImpactDataProjectForm
    {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();

        $builder
            ->add('impact_data', HiddenType::class, [
                'data' => 1
            ])
            ->add('estimationAmount', NumberType::class, [
                'label' => 'regular-title',
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('data', NumberType::class, [
                'label' => 'Number',
                'constraints' => [
                    new Constraints\NotBlank(),
                ]
            ])
            ->add('active', BooleanType::class, [])
        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): ImpactDataProjectForm
    {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();

        $impactDataProject = new ImpactDataProject($data);
        $impactDataProjectRepository = new ImpactDataProjectRepository();

        $errors = [];
        if ($impactDataProjectRepository->persist($impactDataProject, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return $this;
    }
}
