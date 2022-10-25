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
use Goteo\Library\Forms\FormModelException;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Text;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProjectAnalyticsForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getGoogleAnalyticsConstraints(): array
    {
        return [
            new Regex("/^[A-Z][A-Z0-9]?-[A-Z0-9]{4,10}(?:\-[1-9]\d{0,3})?$/")
            ];
    }

    public function createForm(): ProjectAnalyticsForm
    {
        $project = $this->getModel();

        $this->getBuilder()
            ->add('analytics_id', TextType::class, array(
                'label' => 'regular-analytics',
                'required' => false,
                'constraints' => $this->getGoogleAnalyticsConstraints(),
                'attr' => ['help' => Text::get('help-user-analytics')],
            ))
            ->add('facebook_pixel', TextType::class, array(
                'label' => 'regular-facebook-pixel',
                'required' => false,
                'attr' => ['help' => Text::get('help-user-facebook-pixel')],
            ))
            ->add('submit', SubmitType::class, []);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false): ProjectAnalyticsForm
    {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $project = $this->getModel();
        $project->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
