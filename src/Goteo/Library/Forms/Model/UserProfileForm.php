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

use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\LocationType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TitleType;
use Goteo\Util\Form\Type\UrlType;
use Goteo\Util\Form\Type\YearType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Model\User\Web;
use Goteo\Model\User\Interest;
use Goteo\Model\User\UserLocation;
use Goteo\Library\Forms\FormModelException;

class UserProfileForm extends AbstractFormProcessor {

    public function getConstraints($field): array
    {
        $constraints = [];
        if($field === 'name') {
            $constraints[] = new Constraints\NotBlank();
        }
        elseif($this->getFullValidation()) {
            if(in_array($field, ['gender', 'about'])) {
                $constraints[] = new Constraints\NotBlank();
            }
            if(in_array($field, ['webs', 'facebook', 'twitter', 'instagram'] )) {
                $constraints[] = new Constraints\Callback(function($object, ExecutionContextInterface $context) use ($field) {
                    $data = $context->getRoot()->getData();
                    if(empty($data['webs']) && empty($data['facebook']) && empty($data['twitter']) && empty($data['instagram'])) {
                        $context->buildViolation(Text::get('project-validation-error-profile_social'))
                        ->atPath($field)
                        ->addViolation();
                    }

                });
            }
        }
        return $constraints;
    }

    // Remove hidden category num. 15 (test)
    public function getDefaults($sanitize = true) {
        $data = parent::getDefaults($sanitize);
        if(($key = array_search(15, $data['interests'])) !== false) {
            unset($data['interests'][$key]);
        }
        // Do not test images
        unset($data['avatar']);

        if(empty($data['location'])) $data['location'] = null;

        return $data;
    }

    private function getInterestsChoices(): array
    {
        $interestsChoices = [];
        $interests = Interest::getAll();

        foreach ($interests as $key => $value) {
            $interestsChoices[$value] = $key;
        }

        return $interestsChoices;
    }

    private function getOriginRegisterChoices(): array
    {
        $origin_register_choices=[];
        $origin_register=USER::ALL_ORIGIN_REGISTER;

        foreach($origin_register as $option)
            $origin_register_choices[Text::get('profile-field-origin-register-'.$option)]=$option;

        return $origin_register_choices;

    }

    public function createForm() {
        $non_public = '<i class="fa fa-eye-slash"></i> '. Text::get('project-non-public-field');
        $user = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('name', TextareaType::class, [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
            ])
            ->add('location', LocationType::class, [
                'label' => 'profile-field-location',
                'constraints' => $this->getConstraints('location'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'type' => 'user',
                'item' => $user->id,
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
            ])
            ->add('locable', BooleanType::class, [
                'label' => 'dashboard-user-location-locate',
                'constraints' => $this->getConstraints('unlocable'),
                'disabled' => $this->getReadonly(),
                'data' => !$user->unlocable,
                'attr' => ['help' => Text::get('dashboard-user-location-help')],
                'required' => false,
                'color' => 'cyan'
            ])
            ->add('avatar', DropfilesType::class, [
                'label' => 'profile-fields-image-title',
                'constraints' => $this->getConstraints('avatar'),
                'disabled' => $this->getReadonly(),
                'required' => false
            ])
            ;

        if ($user->roles['vip']) {
            // TODO: vip avatar
        }

        $builder
            ->add('birthyear', YearType::class, [
                'label' => 'invest-address-birthyear-field',
                'constraints' => $this->getConstraints('birthyear'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'invest-address-gender-field',
                'constraints' => $this->getConstraints('gender'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'choices' => [
                    Text::get('regular-female') => 'F',
                    Text::get('regular-male') => 'M',
                    Text::get('regular-others') => 'X'
                ],
                'required' => false
            ])
            ->add('legal_entity', ChoiceType::class, [
                'label' => 'profile-field-legal-entity',
                'disabled' => $this->getReadonly(),
                'choices' => [
                    Text::get('profile-field-legal-entity-person') => 0,
                    Text::get('profile-field-legal-entity-self-employed') => 1,
                    Text::get('profile-field-legal-entity-ngo') => 2,
                    Text::get('profile-field-legal-entity-company') => 3,
                    Text::get('profile-field-legal-entity-cooperative') => 4,
                    Text::get('profile-field-legal-entity-asociation') => 5,
                    Text::get('profile-field-legal-entity-others') => 6
                ],
                'required' => false
            ])
            ->add('entity_type', BooleanType::class, [
                'label' => 'profile-field-entity-type-checkbox-public',
                'constraints' => $this->getConstraints('entity_type'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'color' => 'cyan'
            ])
            ->add('origin_register', ChoiceType::class, [
                'label' => 'profile-field-origin-register',
                'constraints' => $this->getConstraints('origin_register'),
                'disabled' => $this->getReadonly(),
                'choices' =>  $this->getOriginRegisterChoices(),
                'required' => false
            ])
            ->add('about', MarkdownType::class, [
                'label' => 'profile-field-about',
                'constraints' => $this->getConstraints('about'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-about')]
            ])
            ->add('interests', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'profile-field-interests',
                'constraints' => $this->getConstraints('interests'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-interests')],
                'choices' => $this->getInterestsChoices(),
                'required' => false
            ])
            ->add('webs', TextareaType::class, [
                'label' => 'profile-field-websites',
                'constraints' => $this->getConstraints('webs'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-webs')],
                'required' => false
            ])
            ->add('social_title', TitleType::class, [
                'label' => 'profile-fields-social-title',
                'required' => false
            ])
            ->add('facebook', UrlType::class, [
                'label' => 'regular-facebook',
                'constraints' => $this->getConstraints('facebook'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-facebook"></i>',
                'attr' => ['help' => Text::get('tooltip-user-facebook'),
                           'placeholder' => Text::get('regular-facebook-url')],
                'required' => false
            ])
            ->add('twitter', TextType::class, [
                'label' => 'regular-twitter',
                'constraints' => $this->getConstraints('twitter'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-twitter"></i>',
                'attr' => ['help' => Text::get('tooltip-user-twitter'),
                           'placeholder' => Text::get('regular-twitter-url')],
                'required' => false
            ])
            ->add('linkedin', UrlType::class, [
                'label' => 'regular-linkedin',
                'constraints' => $this->getConstraints('linkedin'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-linkedin"></i>',
                'attr' => ['help' => Text::get('tooltip-user-linkedin'),
                           'placeholder' => Text::get('regular-linkedin-url')],
                'required' => false
            ])
            ->add('instagram', UrlType::class, [
                'label' => 'regular-instagram',
                'constraints' => $this->getConstraints('instagram'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-instagram"></i>',
                'attr' => ['help' => Text::get('tooltip-user-instagram'),
                           'placeholder' => Text::get('regular-instagram-url')],
                'required' => false
            ]);
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $errors = [];
        $data = $form->getData();
        $user = $this->getModel();
        // Process main image
        if(is_array($data['avatar']) && !empty($data['avatar'])) {
            if (!empty($data['avatar']['removed'])) {
                if ($user->avatar->id == current($data['avatar']['removed'])->id) {
                    $user->user_avatar = [];
                }
            }

            if (!empty($data['avatar']['uploads'])) {
                $uploaded_avatar = $data['avatar']['uploads'][0];
                $user->user_avatar = $uploaded_avatar;

                if($user->user_avatar && $err = $user->user_avatar->getUploadError()) {
                    throw new FormModelException(Text::get('form-sent-error', $err));
                }
            }
        }

        unset($data['avatar']); // do not rebuild data using this

        // Process interests
        // Add "test" interests to those who already have it
        if (array_key_exists('15', $user->interests)) $data['interests'][] = '15';
        $data['interests'] = array_map(function($el) {
                return new Interest(['interest' => $el]);
            }, $data['interests']);

        // Process webs
        $data['webs'] = array_map(function($el) {
                $url = trim($el);
                if(!$url) return null;
                if($url && stripos($url, 'http') !== 0) $url = 'http://' . $url;
                return new Web(['url' => $url]);
            }, explode("\n", $data['webs']));

        // set locable bit
        if(isset($data['locable'])) {
            UserLocation::setProperty($user->id, 'locable', (bool)$data['locable'], $errors);
        }
        $user->rebuildData($data, array_keys($form->all()));
        $user->location = $data['location'] ?: '';

        if (!$user->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
        }

        User::flush();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
