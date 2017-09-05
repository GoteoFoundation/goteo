<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Library\Text;
use Goteo\Library\Check;
use Goteo\Library\Currency;
use Goteo\Application\Message;
use Goteo\Model\User\Apikey;
use Goteo\Model\User\Interest;
use Goteo\Model\User\UserLocation;
use Goteo\Model\User\Web;
use Goteo\Model\User;
use Goteo\Application\Lang;

class SettingsDashboardController extends \Goteo\Core\Controller {
    protected $user;

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->user = Session::getUser();
        $this->contextVars([
            'section' => 'settings'
        ]);
    }


    protected function createSidebar($zone = '') {
        // Create sidebar menu
        Session::addToSidebarMenu( '<i class="icon icon-user"></i> ' . Text::get('dashboard-menu-profile-profile'), '/dashboard/settings', 'profile');
        Session::addToSidebarMenu( '<i class="fa fa-legal"></i> ' . Text::get('dashboard-menu-profile-personal'), '/dashboard/settings/personal', 'personal');
        // Session::addToSidebarMenu( '<i class="fa fa-location-arrow"></i> ' . Text::get('dashboard-menu-profile-location'), '/dashboard/profile/location', 'location');
        Session::addToSidebarMenu( '<i class="fa fa-user-secret"></i> ' . Text::get('dashboard-menu-profile-access'), '/dashboard/settings/access', 'access');
        Session::addToSidebarMenu( '<i class="fa fa-toggle-on"></i> ' . Text::get('dashboard-menu-profile-preferences'), '/dashboard/settings/preferences', 'preferences');
        Session::addToSidebarMenu( '<i class="fa fa-key"></i> ' . Text::get('dashboard-menu-activity-apikey'), '/dashboard/settings/apikey', 'apikey');
        Session::addToSidebarMenu( '<i class="fa fa-vcard"></i> ' . Text::get('dashboard-menu-profile-public'), '/user/profile/' . $this->user->id, 'public');


        View::getEngine()->useData([
            'zone' => $zone,
            // 'sidebarBottom' => [ '/dashboard/settings' => '<i class="fa fa-reply" title="' . Text::get('dashboard-menu-profile-profile') . '"></i> ' . Text::get('dashboard-menu-profile-profile') ]
        ]);

    }

    /**
     * Settings
     */
    public function indexAction(Request $request)
    {
        $this->user = User::flush();
        $this->createSidebar('profile');
        $defaults = (array) $this->user;
        $defaults['entity_type'] = (bool) $defaults['entity_type'];
        $defaults['unlocable'] = UserLocation::isUnlocable($this->user->id);
        $defaults['avatar'] = $this->user->user_avatar ? $this->user->avatar : null;
        $defaults['webs'] = implode("\n", $this->user->webs);

        $builder = $this->createFormBuilder($defaults)
            ->add('name', 'text', [
                'label' => 'profile-field-name'
            ])
            ->add('location', 'location', [
                'label' => 'profile-field-location',
                'type' => 'user',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
            ])
            ->add('unlocable', 'boolean', [
                'label' => 'dashboard-user-location-unlocate',
                'attr' => ['help' => Text::get('dashboard-user-location-help')],
                'required' => false
            ])
            ->add('avatar', 'dropfiles', [
                'label' => 'profile-fields-image-title',
                'required' => false
            ])
            ;

        if ($this->user->roles['vip']) {
            // TODO: vip avatar
        }

        $builder
            ->add('birthyear', 'year', [
                'label' => 'invest-address-birthyear-field',
                'required' => false
            ])
            ->add('gender', 'choice', [
                'label' => 'invest-address-gender-field',
                'choices' => [
                    'F' => Text::get('regular-female'),
                    'M' => Text::get('regular-male'),
                    'X' => Text::get('regular-others')
                ],
                'required' => false
            ])
            ->add('legal_entity', 'choice', [
                'label' => 'profile-field-legal-entity',
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
                'required' => false
            ])
            ->add('about', 'textarea', [
                'label' => 'profile-field-about',
                'attr' => ['help' => Text::get('tooltip-user-about')]
            ])
            ->add('interests', 'choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'profile-field-interests',
                'attr' => ['help' => Text::get('tooltip-user-interests')],
                'choices' => Interest::getAll(),
                'required' => false
            ])
            ->add('keywords', 'tags', [
                'label' => 'profile-field-keywords',
                'attr' => ['help' => Text::get('tooltip-user-keywords')],
                'required' => false,
                'url' => '/api/keywords?q=%QUERY'
            ])
            ->add('contribution', 'textarea', [
                'label' => 'profile-field-contribution',
                'attr' => ['help' => Text::get('tooltip-user-contribution')],
                'required' => false
            ])
            ->add('webs', 'textarea', [
                'label' => 'profile-field-websites',
                'attr' => ['help' => Text::get('tooltip-user-webs')],
                'required' => false
            ])
            ->add('social_title', 'title', [
                'label' => 'profile-fields-social-title',
                'required' => false
            ])
            ->add('facebook', 'url', [
                'label' => 'regular-facebook',
                'pre_addon' => '<i class="fa fa-facebook"></i>',
                'attr' => ['help' => Text::get('tooltip-user-facebook'),
                           'placeholder' => Text::get('regular-facebook-url')],
                'required' => false
            ])
            ->add('twitter', 'url', [
                'label' => 'regular-twitter',
                'pre_addon' => '<i class="fa fa-twitter"></i>',
                'attr' => ['help' => Text::get('tooltip-user-twitter'),
                           'placeholder' => Text::get('regular-twitter-url')],
                'required' => false
            ])
            ->add('google', 'url', [
                'label' => 'regular-google',
                'pre_addon' => '<i class="fa fa-google-plus"></i>',
                'attr' => ['help' => Text::get('tooltip-user-google'),
                           'placeholder' => Text::get('regular-google-url')],
                'required' => false
            ])
            ->add('linkedin', 'url', [
                'label' => 'regular-linkedin',
                'pre_addon' => '<i class="fa fa-linkedin"></i>',
                'attr' => ['help' => Text::get('tooltip-user-linkedin'),
                           'placeholder' => Text::get('regular-linkedin-url')],
                'required' => false
            ])
            ->add('identica', 'url', [
                'label' => 'regular-identica',
                'pre_addon' => '<i class="fa fa-comment-o"></i>',
                'attr' => ['help' => Text::get('tooltip-user-identica'),
                           'placeholder' => Text::get('regular-identica-url')],
                'required' => false
            ])
            ->add('submit', 'submit');

        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $errors = [];
                $data = $form->getData();
                // print_r($data);die;

                // maintain test interest
                if (in_array('15', $this->user->interests)) $data['interests'][] = '15';

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
                    UserLocation::setProperty($this->user->id, 'locable', !$data['unlocable'], $errors);
                }
                $this->user->rebuildData($data);
                if ($this->user->save($errors)) {
                    Message::info(Text::get('user-profile-saved'));
                    // assign translation if no in the default language
                    if ($this->user->lang != Config::get('lang')) {
                        // if (!User::isTranslated($this->user->id, $this->user->lang)) {
                            $this->user->about_lang = $this->user->about;
                            $this->user->keywords_lang = $this->user->keywords;
                            $this->user->contribution_lang = $this->user->contribution;
                            $this->user->saveLang($errors);
                        // }
                    }
                    $this->user = User::flush();
                    if($errors) {
                        Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                    }
                    return $this->redirect('/dashboard/settings');
                } else {
                    Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                }
            }
        }
        return $this->viewResponse('dashboard/settings', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Settings (preferences)
     */
    public function preferencesAction(Request $request)
    {
        $this->createSidebar('preferences');

        $defaults = (array)User::getPreferences($this->user);
        $bools = ['updates', 'threads', 'rounds', 'mailing', 'email', 'tips'];
        foreach($bools as $b) {
            $defaults[$b] = (bool) $defaults[$b];
        }

        // Create the form
        $builder = $this->createFormBuilder($defaults)
            ->add('comlang', 'choice', [
                'label' => 'user-preferences-comlang',
                'choices' => Lang::listAll()
            ]);

        $currencies = Currency::listAll('name');
        if(count($currencies) > 1) {
            $builder->add('currency', 'choice', [
                'label' => 'user-preferences-currency',
                'choices' => $currencies
            ]);
        }


        foreach($bools as $b) {
            $builder
                ->add($b, 'boolean', [
                    'label' => 'user-preferences-' . $b,
                    'color' => 'cyan',
                    'required' => false
                ]);
        }

        $form = $builder->add('submit', 'submit', [
                // 'icon_class' => 'fa fa-plus',
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();
                // var_dump($data);die;

                if (User::setPreferences($this->user, $data, $errors)) {
                    Session::store('currency', $data['currency']);
                    Message::info(Text::get('user-prefer-saved'));
                    return $this->redirect('/dashboard/settings/preferences');
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }
            }
        }
        return $this->viewResponse('dashboard/settings/preferences', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Settings (personal)
     */
    public function personalAction(Request $request)
    {
        $this->createSidebar('personal');

        $defaults = (array)User::getPersonal($this->user);

        // Create the form
        $builder = $this->createFormBuilder($defaults)
            ->add('contract_name', 'text', [
                'label' => 'personal-field-contract_name',
                'attr' => ['help' => Text::get('tooltip-project-contract_name')]
            ])
            ->add('contract_nif', 'text', [
                'label' => 'personal-field-contract_nif',
                'attr' => ['help' => Text::get('tooltip-project-contract_nif')]
            ])
            ->add('phone', 'text', [
                'label' => 'personal-field-phone',
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('address', 'text', [
                'label' => 'personal-field-address',
                // 'attr' => ['help' => Text::get('tooltip-project-address')]
            ])
            ->add('zipcode', 'text', [
                'label' => 'personal-field-zipcode',
                // 'attr' => ['help' => Text::get('tooltip-project-zipcode')]
            ])
            ->add('location', 'text', [
                'label' => 'personal-field-location',
                // 'attr' => ['help' => Text::get('tooltip-project-location')]
            ])
            ->add('country', 'text', [
                'label' => 'personal-field-country',
                // 'attr' => ['help' => Text::get('tooltip-project-country')]
            ])
            ;

        $form = $builder->add('submit', 'submit', [
                // 'icon_class' => 'fa fa-plus',
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();
                // var_dump($data);die;

                if (User::setPersonal($this->user, $data, true, $errors)) {
                    Message::info(Text::get('user-personal-saved'));
                    return $this->redirect('/dashboard/settings/personal');
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }
            }
        }
        return $this->viewResponse('dashboard/settings/personal', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Settings (access)
     */
    public function accessAction(Request $request)
    {
        $this->createSidebar('access');

        // Create the form
        $form1 = $this->createFormBuilder()
            ->add('nemail', 'email', [
                'label' => 'login-register-email-field',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-new_email')]
            ])
            ->add('remail', 'email', [
                'label' => 'login-register-confirm-field',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-confirm_email')]
            ])
            ->add('submit', 'submit', [
                // 'icon_class' => 'fa fa-plus',
            ])->getForm();

        $form2 = $this->createFormBuilder()
            ->add('npassword', 'password', [
                'label' => 'user-changepass-new',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-new_password')]
            ])
            ->add('rpassword', 'password', [
                'label' => 'user-changepass-confirm',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-confirm_password')]
            ])
            ->add('submit', 'submit', [
                // 'icon_class' => 'fa fa-plus',
            ])->getForm();

        $change = $errors = [];
        $form1->handleRequest($request);
        if ($form1->isSubmitted()) {
            if($form1->isValid()) {
                $data = $form1->getData();

                if (empty($data['nemail'])) {
                    $errors['email'] = Text::get('error-user-email-empty');
                } elseif (!Check::mail($data['nemail'])) {
                    $errors['email'] = Text::get('error-user-email-invalid');
                } elseif (empty($data['remail'])) {
                    $errors['email_retry'] = Text::get('error-user-email-empty');
                } elseif (strcmp($data['nemail'], $data['remail']) !== 0) {
                    $errors['email_retry'] = Text::get('error-user-email-confirm');
                } else {
                    // var_dump($data);die;
                    $this->user->email = $data['email'];
                    $change['email'] = Text::get('user-email-change-sent');
                }
            }
        }
        $form2->handleRequest($request);
        if ($form2->isSubmitted()) {
            if($form2->isValid()) {
                $data = $form2->getData();

                if (empty($data['npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-empty');
                } elseif (!Check::password($data['npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-invalid');
                } elseif (empty($data['rpassword'])) {
                    $errors['password_retry'] = Text::get('error-user-password-empty');
                } elseif (strcmp($data['npassword'], $data['rpassword']) !== 0) {
                    $errors['password_retry'] = Text::get('error-user-password-confirm');
                } else {
                    $this->user->password = $data['password'];
                    $change['password'] = Text::get('user-password-changed');
                }
            }
        }

        if($change) {
            if ($this->user->save($errors)) {
                foreach($change as $t) {
                    Message::info($t);
                }

                $this->user = User::flush();
                return $this->redirect('/dashboard/settings/access');
            }
        }
        if($errors) {
            Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
        }
        return $this->viewResponse('dashboard/settings/access', [
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
            'form1' => $form1->createView(),
            'form2' => $form2->createView()
        ]);
    }


    /**
     * API key
     */
    public function apikeyAction(Request $request)
    {
        $this->createSidebar('apikey');

        $defaults = [
            'user_id' => $this->user->id,
            'key' => Apikey::get($this->user->id)
        ];
        // Create the form
        $form = $this->createFormBuilder($defaults)
            ->add('submit', 'submit', [
                'icon_class' => 'fa fa-plus',
                'label' => 'api-key-generate-new'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $defaults['key'] = md5($this->user->id . date('dMYHis'));
                $apikey = new Apikey($defaults);

                if($apikey->save($errors)) {
                    // print_r($post);die;
                    Message::info(Text::get('form-sent-success'));
                    return $this->redirect();
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }

            } else {
                Message::error(Text::get('form-has-errors'));
            }
        }
        return $this->viewResponse('dashboard/settings/apikey', $defaults + ['form' => $form->createView()]);
    }

}
