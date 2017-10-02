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

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;


class ProjectCostsForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $project = $this->getModel();
        $this->getBuilder()
            ->add('one_round', 'choice', [
                'label' => 'costs-field-select-rounds',
                'constraints' => array(new Constraints\NotBlank()),
                'required' => true,
                'expanded' => true,
                'wrap_class' => 'col-xs-6',
                'choices' => [
                    '1' => Text::get('project-one-round'),
                    '0' => Text::get('project-two-rounds')
                ],
                'attr' => ['help' => '<span class="' . ($project->one_round ? '': 'hidden') . '">' . Text::get('tooltip-project-rounds') . '</span><span class="' . ($project->one_round ? 'hidden': '') . '">' . Text::get('tooltip-project-2rounds') . '</span>']
            ])
            ;
        return $this;
    }

}
