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
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\User\Web;
use Goteo\Model\User\Interest;
use Goteo\Model\User\UserLocation;
use Goteo\Library\Forms\FormModelException;

class UserProfileForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {

        $builder = $this->getBuilder()
            ->add('name', 'text', [
                'disabled' => $this->getReadonly(),
                'label' => 'profile-field-name'
            ])
            ->add('location', 'location', [
                'label' => 'profile-field-location',
                'disabled' => $this->getReadonly(),
                'type' => 'user',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
            ])
            ->add('unlocable', 'boolean', [
                'label' => 'dashboard-user-location-unlocate',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('dashboard-user-location-help')],
                'required' => false,
                'color' => 'cyan'
            ])
            ->add('avatar', 'dropfiles', [
                'label' => 'profile-fields-image-title',
                'disabled' => $this->getReadonly(),
                'required' => false
            ])
            ;

        if ($user->roles['vip']) {
            // TODO: vip avatar
        }

        $builder
            ->add('birthyear', 'year', [
                'label' => 'invest-address-birthyear-field',
                'disabled' => $this->getReadonly(),
                'required' => false
            ])
            ->add('gender', 'choice', [
                'label' => 'invest-address-gender-field',
                'disabled' => $this->getReadonly(),
                'choices' => [
                    'F' => Text::get('regular-female'),
                    'M' => Text::get('regular-male'),
                    'X' => Text::get('regular-others')
                ],
                'required' => false
            ])
            ->add('legal_entity', 'choice', [
                'label' => 'profile-field-legal-entity',
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
                'disabled' => $this->getReadonly(),
                'required' => false,
                'color' => 'cyan'
            ])
            ->add('about', 'textarea', [
                'label' => 'profile-field-about',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-about')]
            ])
            ->add('interests', 'choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'profile-field-interests',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-interests')],
                'choices' => Interest::getAll(),
                'required' => false
            ])
            ->add('keywords', 'tags', [
                'label' => 'profile-field-keywords',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-keywords')],
                'required' => false,
                'url' => '/api/keywords?q=%QUERY'
            ])
            ->add('contribution', 'textarea', [
                'label' => 'profile-field-contribution',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-user-contribution')],
                'required' => false
            ])
            ->add('webs', 'textarea', [
                'label' => 'profile-field-websites',
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
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-facebook"></i>',
                'attr' => ['help' => Text::get('tooltip-user-facebook'),
                           'placeholder' => Text::get('regular-facebook-url')],
                'required' => false
            ])
            ->add('twitter', 'url', [
                'label' => 'regular-twitter',
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-twitter"></i>',
                'attr' => ['help' => Text::get('tooltip-user-twitter'),
                           'placeholder' => Text::get('regular-twitter-url')],
                'required' => false
            ])
            ->add('google', 'url', [
                'label' => 'regular-google',
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-google-plus"></i>',
                'attr' => ['help' => Text::get('tooltip-user-google'),
                           'placeholder' => Text::get('regular-google-url')],
                'required' => false
            ])
            ->add('linkedin', 'url', [
                'label' => 'regular-linkedin',
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-linkedin"></i>',
                'attr' => ['help' => Text::get('tooltip-user-linkedin'),
                           'placeholder' => Text::get('regular-linkedin-url')],
                'required' => false
            ])
            // ->add('identica', 'url', [
            //     'label' => 'regular-identica',
            //     'disabled' => $this->getReadonly(),
            //     'pre_addon' => '<i class="fa fa-comment-o"></i>',
            //     'attr' => ['help' => Text::get('tooltip-user-identica'),
            //                'placeholder' => Text::get('regular-identica-url')],
            //     'required' => false
            // ])
            ->add('instagram', 'url', [
                'label' => 'regular-instagram',
                'disabled' => $this->getReadonly(),
                'pre_addon' => '<i class="fa fa-instagram"></i>',
                'attr' => ['help' => Text::get('tooltip-user-instagram'),
                           'placeholder' => Text::get('regular-instagram-url')],
                'required' => false
            ]);
        return $this;
    }

    public function save(FormInterface $form = null) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        $errors = [];
        $data = $form->getData();
        $user = $this->getModel();
        // print_r($data);die;

        // maintain test interest
        if (in_array('15', $user->interests)) $data['interests'][] = '15';

        // Process main image
        if(is_array($data['avatar'])) {
            $data['avatar'] = current($data['avatar']);
        }
        $data['user_avatar'] = $data['avatar'];
        unset($data['avatar']); // do not rebuild data using this

        // Process webs
        $data['webs'] = array_map(function($el) {
                $url = trim($el);
                if($url && stripos($url, 'http') !== 0) $url = 'http://' . $url;
                return new Web(['url' => $url]);
            }, explode("\n", $data['webs']));

        // set locable bit
        if(isset($data['unlocable'])) {
            UserLocation::setProperty($user->id, 'locable', !$data['unlocable'], $errors);
        }
        $user->rebuildData($data, array_keys($form->all()));
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

        if($errors) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
        }

        return $this;
    }
}
