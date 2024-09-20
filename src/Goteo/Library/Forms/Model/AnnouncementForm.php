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

use Goteo\Application\Session;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Announcement;
use Goteo\Repository\AnnouncementRepository;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\UrlType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;


class AnnouncementForm extends AbstractFormProcessor
{

    public function createForm(): AnnouncementForm
    {
        /** @var Announcement $announcement */
        $announcement = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('title', TextType::class, [
                'label' => 'regular-title',
                'required' => true,
                'data' => $announcement->title,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'regular-description',
                'required' => true,
                'data' => $announcement->description,
                'constraints' => [
                    new Constraints\NotBlank(),
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'regular-type',
                'required' => true,
                'data' => $announcement->type,
                'choices' => $this->getAnnouncementTypes()
            ])
            ->add('cta_url', UrlType::class, [
                'label' => 'regular-cta',
                'required' => false,
                'data' => $announcement->cta_url,
                'attr' => ['help' => Text::get('admin-announcement-cta-text')]
            ])
            ->add('cta_text', TextType::class, [
                'label' => 'regular-cta-text',
                'required' => false,
                'data' => $announcement->cta_text,
                'attr' => ['help' => Text::get('admin-announcement-cta-text')]
            ])
            ->add('active', BooleanType::class, [
                'label' => 'regular-active',
                'required' => false,
                'data' => $announcement->active
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ]);

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        if (!$form) $form = $this->getBuilder()->getForm();
        if (!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();

        /** @var Announcement $announcement */
        $announcement = $this->getModel();

        $announcement->title = $data['title'];
        $announcement->description = $data['description'];
        $announcement->active = $data['active'];
        $announcement->type = $data['type'];
        $announcement->lang = Session::get('lang');

        if (isset($data['cta_url']))
            $announcement->cta_url = $data['cta_url'];

        if (isset($data['cta_text']))
            $announcement->cta_text = $data['cta_text'];

        $announcementRepository = new AnnouncementRepository();

        $errors = [];
        $announcementRepository->persist($announcement, $errors);

        return $this;
    }

    private function getAnnouncementTypes(): array
    {
        $types =  Announcement::getTypes();
        $ret = [];

        foreach ($types as $type) {
            $ret[Text::get("admin-announcements-type-$type")] = $type;
        }

        return $ret;
    }
}
