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

use Goteo\Library\Forms\Model\UserProfileForm;
use Goteo\Library\Forms\Model\UserPreferencesForm;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\EmailType;
use Goteo\Util\Form\Type\PasswordType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
use Goteo\Model\Contract;

class SettingsDashboardController extends DashboardController
{
    protected $user;

    public function __construct() {
        parent::__construct();
        $this->contextVars([
            'section' => 'settings'
        ]);
    }

    protected function createSettingsSidebar($zone = '') {
        Session::addToSidebarMenu( '<i class="icon icon-2x icon-user"></i> ' . Text::get('dashboard-menu-profile-profile'), '/dashboard/settings', 'profile');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-legal"></i> ' . Text::get('dashboard-menu-profile-personal'), '/dashboard/settings/personal', 'personal');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-user-secret"></i> ' . Text::get('dashboard-menu-profile-access'), '/dashboard/settings/access', 'access');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-toggle-on"></i> ' . Text::get('dashboard-menu-profile-preferences'), '/dashboard/settings/preferences', 'preferences');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-key"></i> ' . Text::get('dashboard-menu-activity-apikey'), '/dashboard/settings/apikey', 'apikey');
        Session::addToSidebarMenu( '<i class="fa fa-2x fa-fw fa-vcard"></i> ' . Text::get('dashboard-menu-profile-public'), '/user/profile/' . $this->user->id, 'public');

        View::getEngine()->useData([
            'zone' => $zone,
        ]);
    }

    /**
     * Settings: profile edit
     */
    public function profileAction(Request $request, $pid = null)
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

        $processor = $this->getModelForm(UserProfileForm::class, $user, $defaults, [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', SubmitType::class, [
                'label' => $submit_label ?: 'regular-submit'
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
        $user = $this->user;
        $translated = $user->getLangsAvailable();
        $this->createSettingsSidebar('profile');

        $defaults = (array) $user->getLang($lang);
        if(empty($defaults['name'])) $defaults['name'] = $user->name;

        $builder = $this->createFormBuilder($defaults, 'autoform', ['attr' => ['class' => 'autoform hide-help']])
            ->add('name', TextType::class, [
                'label' => 'regular-name',
                'attr' => ['help' => $user->name]
            ])
            ->add('about', TextareaType::class, [
                'label' => 'profile-field-about',
                'attr' => ['help' => $user->about]
            ])
            ->add('submit', SubmitType::class)
            ->add('remove', SubmitType::class, [
                'label' => Text::get('translator-delete', $languages[$lang]),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right-form btn btn-default btn-lg',
                    'data-confirm' => Text::get('translator-delete-sure', $languages[$lang])
                ]
            ]);

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
                $data['keywords'] = $user->keywords; // Do not translate keywords for the moment
                if($user->setLang($lang, $data, $errors)) {
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


    public function preferencesAction(Request $request)
    {
        $this->createSettingsSidebar('preferences');

        $userPreferences = (array)User::getPreferences($this->user);
        $processor = $this->getModelForm(UserPreferencesForm::class, $this->user, $userPreferences, [], $request);
        $processor->createForm();
        $form = $processor->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form, true);
                return $this->redirect('/dashboard/settings/preferences');
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('dashboard/settings/preferences', [
            'form' => $form->createView()
        ]);
    }

    public function personalAction(Request $request)
    {
        $this->createSettingsSidebar('personal');

        $defaults = (array)User::getPersonal($this->user);

        // Create the form
        $builder = $this->createFormBuilder($defaults)
            ->add('contract_name', TextType::class, [
                'label' => 'personal-field-contract_name',
                'attr' => ['help' => Text::get('tooltip-project-contract_name')]
            ])
            ->add('contract_nif', TextType::class, [
                'label' => 'personal-field-contract_nif',
                'attr' => ['help' => Text::get('tooltip-project-contract_nif')]
            ])
            ->add('contract_legal_document_type', ChoiceType::class, [
                'label' => 'personal-field-contract_legal_document_type',
                'choices' => Contract::getNaturalPersonDocumentTypes(),
                'attr' => ['help' => Text::get('tooltip-project-contract_nif')],
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => 'personal-field-phone',
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('address', TextType::class, [
                'label' => 'personal-field-address',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'personal-field-zipcode',
            ])
            ->add('location', TextType::class, [
                'label' => 'personal-field-location',
            ])
            ->add('country', TextType::class, [
                'label' => 'personal-field-country',
            ])
            ;

        $form = $builder->add('submit', SubmitType::class, [])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();

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

    public function accessAction(Request $request)
    {
        $this->createSettingsSidebar('access');

        $form1 = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'user-changepass-old',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-user_password')]
            ])
            ->add('nemail', EmailType::class, [
                'label' => 'login-register-email-field',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-new_email')]
            ])
            ->add('remail', EmailType::class, [
                'label' => 'login-register-confirm-field',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-confirm_email')]
            ])
            ->add('submit', SubmitType::class, [])
            ->getForm();

        $form2 = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'user-changepass-old',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-user_password')]
            ])
            ->add('npassword', PasswordType::class, [
                'label' => 'user-changepass-new',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-new_password')]
            ])
            ->add('rpassword', PasswordType::class, [
                'label' => 'user-changepass-confirm',
                'attr' => ['help' => Text::get('tooltip-dashboard-user-confirm_password')]
            ])
            ->add('submit', SubmitType::class, [])
            ->getForm();

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

    public function apikeyAction(Request $request)
    {
        $this->createSettingsSidebar('apikey');

        $defaults = [
            'user_id' => $this->user->id,
            'key' => Apikey::get($this->user->id)
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('submit', SubmitType::class, [
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
