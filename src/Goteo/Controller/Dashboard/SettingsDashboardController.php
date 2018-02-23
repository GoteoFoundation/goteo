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

use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Library\Text;
use Goteo\Library\Check;
use Goteo\Application\Currency;
use Goteo\Application\Message;
use Goteo\Model\User\Apikey;
use Goteo\Model\User\UserLocation;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Application\Lang;
use Goteo\Library\Forms\FormModelException;
use Goteo\Controller\DashboardController;

class SettingsDashboardController extends DashboardController {
    protected $user;

    public function __construct() {
        parent::__construct();
        $this->contextVars([
            'section' => 'settings'
        ]);
    }


    protected function createSettingsSidebar($zone = '') {
        // Create sidebar menu
        Session::addToSidebarMenu( '<i class="icon icon-2x icon-user"></i> ' . Text::get('dashboard-menu-profile-profile'), '/dashboard/settings', 'profile');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-legal"></i> ' . Text::get('dashboard-menu-profile-personal'), '/dashboard/settings/personal', 'personal');
        // Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-location-arrow"></i> ' . Text::get('dashboard-menu-profile-location'), '/dashboard/profile/location', 'location');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-user-secret"></i> ' . Text::get('dashboard-menu-profile-access'), '/dashboard/settings/access', 'access');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-toggle-on"></i> ' . Text::get('dashboard-menu-profile-preferences'), '/dashboard/settings/preferences', 'preferences');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-key"></i> ' . Text::get('dashboard-menu-activity-apikey'), '/dashboard/settings/apikey', 'apikey');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-vcard"></i> ' . Text::get('dashboard-menu-profile-public'), '/user/profile/' . $this->user->id, 'public');


        View::getEngine()->useData([
            'zone' => $zone,
            // 'sidebarBottom' => [ '/dashboard/settings' => '<i class="fa fa-reply" title="' . Text::get('dashboard-menu-profile-profile') . '"></i> ' . Text::get('dashboard-menu-profile-profile') ]
        ]);

    }

    /**
     * Settings: profile edit
     */
    public function profileAction($pid = null, Request $request)
    {
        if($pid) {
            $project = Project::get( $pid );
            // TODO: implement translation permissions
            if(!$project instanceOf Project || !$project->userCanEdit($this->user)) {
                throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
            }

            $user = $project->getOwner();
            ProjectDashboardController::createProjectSidebar($project, 'profile');
            $this->contextVars([
                'section' => 'projects'
            ]);
            if($project->isApproved()) {
                $redirect = '/dashboard/project/' . $pid . '/profile';
            } else {
                $redirect = '/dashboard/project/' . $pid . '/overview';
                $submit_label = 'form-next-button';
            }

        } else {
            $user = User::get($this->user->id, Config::get('lang')); // default system lang
            $redirect = '/dashboard/settings';
            $this->createSettingsSidebar('profile');
        }

        $defaults = (array) $user;
        $defaults['unlocable'] = UserLocation::isUnlocable($user->id);
        $defaults['avatar'] = $user->user_avatar ? $user->avatar : null;
        $defaults['webs'] = implode("\n", $user->webs);
        $defaults['interests'] = array_map(function($i){ return $i->interest; }, $user->interests);

        $processor = $this->getModelForm('UserProfile', $user, $defaults, [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ]);
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('user-profile-saved'));
                return $this->redirect($redirect);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/settings/profile', [
            'form' => $form->createView(),
            'languages' => Lang::listAll('name', false),
            'translated' => $user->getLangsAvailable()
        ]);
    }

    /**
     * Settings: profile edit
     */
    public function profileTranslateAction($lang, Request $request)
    {
        $languages = Lang::listAll('name', false);
        if(!isset($languages[$lang])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/settings/profile');
        }
        // $user = User::get($this->user->id);
        $user = $this->user;
        $translated = $user->getLangsAvailable();
        $this->createSettingsSidebar('profile');

        $defaults = (array) $user->getLang($lang);
        if(empty($defaults['name'])) $defaults['name'] = $user->name;

        $builder = $this->createFormBuilder($defaults, 'autoform', ['attr' => ['class' => 'autoform hide-help']])
            ->add('name', 'text', [
                'label' => 'regular-name',
                'attr' => ['help' => $user->name]
            ])
            ->add('about', 'textarea', [
                'label' => 'profile-field-about',
                'attr' => ['help' => $user->about]
            ])
            // ->add('contribution', 'textarea', [
            //     'label' => 'profile-field-contribution',
            //     'attr' => ['help' => Text::get('tooltip-user-contribution')],
            //     'required' => false
            // ])
            ->add('submit', 'submit')
            ->add('remove', 'submit', [
                'label' => Text::get('translator-delete', $languages[$lang]),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right-form btn btn-default btn-lg',
                    'data-confirm' => Text::get('translator-delete-sure', $languages[$lang])
                    ]

            ]);

            ;
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $errors = [];

                // Check if we want to remove a translation
                if($form->get('remove')->isClicked()) {
                    if($user->removeLang($lang)) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    }
                    return $this->redirect('/dashboard/settings/profile');
                }

                $data = $form->getData();
                $user->lang = $lang;
                // print_r($data);die($form->getClickedButton()->getName());
                $user->name_lang = $data['name'];
                $user->about_lang = $data['about'];
                // $user->keywords_lang = $data['keywords'];
                $user->keywords_lang = $user->keywords; // Do not translate keywords for the moment
                // $user->contribution_lang = $data['contribution'];
                if($user->saveLang($errors)) {
                    Message::info(Text::get('dashboard-translate-profile-ok', $languages[$lang]));
                    return $this->redirect('/dashboard/settings/profile');
                } else {
                    Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                }

            }
        }
        return $this->viewResponse('dashboard/settings/translate', [
            'form' => $form->createView(),
            'languages' => $languages,
            'translated' => $translated,
            'current' => $lang,
        ]);
    }

    /**
     * Settings (preferences)
     */
    public function preferencesAction(Request $request)
    {
        $this->createSettingsSidebar('preferences');

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
        $this->createSettingsSidebar('personal');

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
        $this->createSettingsSidebar('access');

        // Create the form
        $form1 = $this->createFormBuilder()
            ->add('password', 'password', [
                'label' => 'user-changepass-old',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-user_password')]
            ])
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
            ->add('password', 'password', [
                'label' => 'user-changepass-old',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-user_password')]
            ])
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

                if(!$this->user->validatePassword($data['password'])) {
                    $errors['password'] = Text::get('error-user-wrong-password');
                } elseif (empty($data['nemail'])) {
                    $errors['email'] = Text::get('error-user-email-empty');
                } elseif (!Check::mail($data['nemail'])) {
                    $errors['email'] = Text::get('error-user-email-invalid');
                } elseif (empty($data['remail'])) {
                    $errors['email_retry'] = Text::get('error-user-email-empty');
                } elseif (strcmp($data['nemail'], $data['remail']) !== 0) {
                    $errors['email_retry'] = Text::get('error-user-email-confirm');
                } elseif ($this->user->setEmail($data['nemail'], $errors)) {
                    $change['email'] = Text::get('user-email-change-sent');
                }
            }
        }
        $form2->handleRequest($request);
        if ($form2->isSubmitted()) {
            if($form2->isValid()) {
                $data = $form2->getData();

                if(!$this->user->validatePassword($data['password'])) {
                    $errors['password'] = Text::get('error-user-wrong-password');
                } elseif (empty($data['npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-empty');
                } elseif (!Check::password($data['npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-invalid');
                } elseif (empty($data['rpassword'])) {
                    $errors['password_retry'] = Text::get('error-user-password-empty');
                } elseif (strcmp($data['npassword'], $data['rpassword']) !== 0) {
                    $errors['password_retry'] = Text::get('error-user-password-confirm');
                } else {
                    if($this->user->setPassword($data['npassword'], $errors)) {
                        $change['password'] = Text::get('user-password-changed');
                        // Migrate session
                        Session::getSession()->migrate(false, Session::getSessionExpires());
                    }
                }
            }
        }

        if($change) {
            foreach($change as $t) {
                Message::info($t);
            }

            if($this->user->id === Session::getUserId()) {
                $this->user = User::flush();
            }
            return $this->redirect('/dashboard/settings/access');
        }

        if($errors) {
            Message::error(Text::get('form-sent-error', implode(',',$errors)));
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
        $this->createSettingsSidebar('apikey');

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
