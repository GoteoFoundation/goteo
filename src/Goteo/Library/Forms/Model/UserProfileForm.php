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

    public function getConstraints($field) {
        $constraints = [];
        if($field === 'name') {
            $constraints[] = new Constraints\NotBlank();
        }
        elseif($this->getFullValidation()) {
            if(in_array($field, ['gender', 'about'])) {
                $constraints[] = new Constraints\NotBlank();
            }
            if(in_array($field, ['webs', 'facebook', 'twitter'] )) {
                $constraints[] = new Constraints\Callback(function($object, ExecutionContextInterface $context) use ($field) {
                    $data = $context->getRoot()->getData();
                    if(empty($data['webs']) && empty($data['facebook']) && empty($data['twitter'])) {
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
        // var_dump($data);die;
        unset($data['avatar']);

        if(empty($data['location'])) $data['location'] = null;

        return $data;
    }


    public function createForm() {
        $non_public = '<i class="fa fa-eye-slash"></i> '. Text::get('project-non-public-field');
        $user = $this->getModel();

        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $defaults = $options['data'];
        // print_r($defaults);die;
        $builder
            ->add('name', 'text', [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
            ])
// ;return $this;$builder
            ->add('location', 'location', [
                'label' => 'profile-field-location',
                'constraints' => $this->getConstraints('location'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'type' => 'user',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
            ])
            ->add('locable', 'boolean', [
                'label' => 'dashboard-user-location-locate',
                'constraints' => $this->getConstraints('unlocable'),
                'disabled' => $this->getReadonly(),
                'data' => !$user->unlocable,
                'attr' => ['help' => Text::get('dashboard-user-location-help')],
                'required' => false,
                'color' => 'cyan'
            ])
            // ->add('avatar', 'dropfiles', [
            //     'label' => 'profile-fields-image-title',
            //     'constraints' => $this->getConstraints('avatar'),
            //     'disabled' => $this->getReadonly(),
            //     'required' => false
            // ])
            ->add('avatar', 'dropfiles', [
                'label' => 'profile-fields-image-title',
                'constraints' => $this->getConstraints('avatar'),
                'disabled' => $this->getReadonly(),
                'url' => '/api/users/' . $user->id . '/avatar',
                'required' => false
            ])
            ;

        if ($user->roles['vip']) {
            // TODO: vip avatar
        }

        $builder
            ->add('birthyear', 'year', [
                'label' => 'invest-address-birthyear-field',
                'constraints' => $this->getConstraints('birthyear'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'required' => false
            ])
            ->add('gender', 'choice', [
                'label' => 'invest-address-gender-field',
                'constraints' => $this->getConstraints('gender'),
                'disabled' => $this->getReadonly(),
                'attr' =>['info' => $non_public],
                'choices' => [
                    'F' => Text::get('regular-female'),
                    'M' => Text::get('regular-male'),
                    'X' => Text::get('regular-others')
                ],
                'required' => false
            ])
            ->add('legal_entity', 'choice', [
                'label' => 'profile-field-legal-entity',
                'constraints' => $this->getConstraints('legal_entity'),
                'disabled' => $this->getReadonly(),
                'choices' => [
                    '0' => Text::get('profile-field-legal-entity-person'),
                    '1' => Text::get('profile-field-legal-entity-self-employed'),
                    '2' => Text::get('profile-field-legal-entity-ngo'),
                    '3' => Text::get('profile-field-legal-entity-company'),
                    '4' => Text::get('profile-field-legal-entity-cooperative'),
                    '5' => Text::get('profile-field-legal-entity-asociation'),
                    '6' => Text::get('profile-field-legal-entity-others')
                ],
                'required' => false
            ])
            ->add('entity_type', 'boolean', [
                'label' => 'profile-field-entity-type-checkbox-public',
                'constraints' => $this->getConstraints('entity_type'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'color' => 'cyan'
            ])
            ->add('about', 'markdown', [
                'label' => 'profile-field-about',
                'constraints' => $this->getConstraints('about'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-about')]
            ])
            ->add('interests', 'choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'profile-field-interests',
                'constraints' => $this->getConstraints('interests'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-interests')],
                'choices' => Interest::getAll(),
                'required' => false
            ])
            ->add('keywords', 'tags', [
                'label' => 'profile-field-keywords',
                'constraints' => $this->getConstraints('keywords'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-keywords')],
                'required' => false,
                'url' => '/api/keywords?q=%QUERY'
            ])
            // ->add('contribution', 'textarea', [
            //     'label' => 'profile-field-contribution',
            //     'constraints' => $this->getConstraints('contribution'),
            //     'disabled' => $this->getReadonly(),
            //     'attr' => ['help' => Text::get('tooltip-user-contribution')],
            //     'required' => false
            // ])
            ->add('webs', 'textarea', [
                'label' => 'profile-field-websites',
                'constraints' => $this->getConstraints('webs'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-webs')],
                'required' => false
            ])
            ->add('social_title', 'title', [
                'label' => 'profile-fields-social-title',
                'required' => false
            ])
            ->add('facebook', 'url', [
                'label' => 'regular-facebook',
                'constraints' => $this->getConstraints('facebook'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-facebook"></i>',
                'attr' => ['help' => Text::get('tooltip-user-facebook'),
                           'placeholder' => Text::get('regular-facebook-url')],
                'required' => false
            ])
            ->add('twitter', 'text', [
                'label' => 'regular-twitter',
                'constraints' => $this->getConstraints('twitter'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-twitter"></i>',
                'attr' => ['help' => Text::get('tooltip-user-twitter'),
                           'placeholder' => Text::get('regular-twitter-url')],
                'required' => false
            ])
            ->add('google', 'url', [
                'label' => 'regular-google',
                'constraints' => $this->getConstraints('google'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-google-plus"></i>',
                'attr' => ['help' => Text::get('tooltip-user-google'),
                           'placeholder' => Text::get('regular-google-url')],
                'required' => false
            ])
            ->add('linkedin', 'url', [
                'label' => 'regular-linkedin',
                'constraints' => $this->getConstraints('linkedin'),
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-linkedin"></i>',
                'attr' => ['help' => Text::get('tooltip-user-linkedin'),
                           'placeholder' => Text::get('regular-linkedin-url')],
                'required' => false
            ])
            // ->add('identica', 'url', [
            //     'label' => 'regular-identica',
            //     'constraints' => $this->getConstraints('identica'),
            //     'disabled' => $this->getReadonly(),
            //     'pre_addon' => '<i class="fa fa-comment-o"></i>',
            //     'attr' => ['help' => Text::get('tooltip-user-identica'),
            //                'placeholder' => Text::get('regular-identica-url')],
            //     'required' => false
            // ])
            ->add('instagram', 'url', [
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
        // var_dump($data);die;
        // Process main image
        if(is_array($data['avatar'])) {
            $data['avatar'] = reset($data['avatar']);
        }
        $user->user_avatar = $data['avatar'];
        if($user->user_avatar && $err = $user->user_avatar->getUploadError()) {
            throw new FormModelException(Text::get('form-sent-error', $err));

        }
        // $data['user_avatar'] = $data['avatar'];
        unset($data['avatar']); // do not rebuild data using this

        // Process interests
        // Add "test" interes to those who already have it
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
        $user->location = $data['location'] ? $data['location'] : '';

        if (!$user->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
        }

        //
        // This is commented on purpose, see: Goteo\Model\User::get()
        //
        // // assign translation if no in the default language
        // if ($user->lang != Config::get('lang')) {
        //     // if (!User::isTranslated($this->user->id, $this->user->lang)) {
        //         $user->about_lang = $user->about;
        //         $user->keywords_lang = $user->keywords;
        //         $user->contribution_lang = $user->contribution;
        //         $user->saveLang($errors);
        //     // }
        // }
        // if($errors) {
        //     throw new FormModelException(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
        // }

        User::flush();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }
}
