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
use Goteo\Application\AppEvents;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Support;
use Goteo\Model\Project\Cost;
use Goteo\Model\Project\Reward;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\Message as Comment;
use Goteo\Library\Text;
use Goteo\Console\UsersSend;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Event\FilterMessageEvent;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Forms\FormModelException;
use Goteo\Controller\Dashboard\ProjectDashboardController;

class TranslateProjectDashboardController extends ProjectDashboardController {

    /**
     * Additional common context vars for the views
     */
    public function validateProject($pid = null, $section = 'summary', $lang = null, &$form = null, $lang_check = null) {
        $project = parent::validateProject($pid, $section, $lang, $form);
        if(!$project instanceOf Project) return $project;
        $project->rewards = array_merge($project->individual_rewards, $project->social_rewards);
        $languages = Lang::listAll('name', false);
        if($lang_check && !isset($languages[$lang_check])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        }

        $data = [
            'zones' => [
                // 'profile' => Text::get('step-1'),
                'overview' => Text::get('step-main'),
                'costs' => Text::get('step-4'),
                'rewards' => Text::get('step-5'),
                'supports' => Text::get('step-6'),
                // 'updates' => Text::get('project-menu-updates')
            ],
            'languages' => $languages,
            'translated' => array_diff($project->getLangsAvailable(), [$project->lang])
        ];
        if($lang_check) {
            $cost = reset($project->costs);
            $reward = reset($project->rewards);
            $support = reset($project->supports);
            $data['percents'] = [
                'overview' => $project->getLangsPercent($lang_check),
                'costs' => $cost ? $cost->getLangsGroupPercent($lang_check, ['project']) : 0,
                'rewards' => $reward ? $reward->getLangsGroupPercent($lang_check, ['project']) : 0,
                'supports' => $support ? $support->getLangsGroupPercent($lang_check, ['project']) : 0
            ];
        }

        View::getEngine()->useData($data);
        return $project;
    }

    /**
     * Some handy defaults for the form
     */
    public function createFormBuilder($defaults = null, $name = 'autoform', array $options = ['attr' => ['class' => 'autoform hide-help']]) {
        return parent::createFormBuilder($defaults, $name, $options);
    }

    /**
     * Handy method to get a form builder
     * @return Goteo\Library\Forms\FormProcessorInterface
     */
    public function getModelForm($form, Model $model, array $defaults = [], array $options = [], Request $request = null) {
        $finder = $this->getService('app.forms.finder');
        $finder->setModel($model);
        $validate = $mock_validation = false;
        if($request) {
            $validate = $request->query->has('validate');
            $mock_validation = $validate && $request->isMethod('get');
        }
        // $finder->setBuilder($this->createFormBuilder($defaults, 'autoform', $mock_validation ? ['csrf_protection' => false] : []));
        // $finder->setBuilder($this->createFormBuilder($defaults));
        // TODO: a better way to create a csrf_protection without showing errors CSRF on mock_validation
        $finder->setBuilder($this->createFormBuilder($defaults, 'autoform', ['csrf_protection' => false, 'attr' => ['class' => 'autoform hide-help']]));
        $processor = $finder->getInstance($form, $options);
        // Set full validation if required in Request
        // Do a fake submit of the form on create to test errors (only on GET requests)
        $processor->setFullValidation($validate, $mock_validation);

        return $processor;
    }

    /**
     * Index translator
     */
    public function translateAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'translate');
        if($project instanceOf Response) return $project;

        $translated = $project->getLangsAvailable();
        if($cost = reset($project->costs)) {
            $translated = array_merge($translated, $cost->getLangsAvailable());
        }
        $translated = array_unique(array_diff($translated, [$project->lang]));

        $cost = reset($project->costs);
        $reward = reset($project->rewards);
        $support = reset($project->supports);
        $percents = [];
        foreach($translated as $lang) {
            $percents[$lang] = [
                'overview' => $project->getLangsPercent($lang),
                'costs' => $cost ? $cost->getLangsGroupPercent($lang, ['project']) : 0,
                'rewards' => $reward ? $reward->getLangsGroupPercent($lang, ['project']) : 0,
                'supports' => $support ? $support->getLangsGroupPercent($lang, ['project']) : 0
            ];
        }

        return $this->viewResponse('dashboard/project/translate/index', [
            'translated' => $translated,
            'percents' => $percents
        ]);

    }

    /**
     * Project overview translator
     */
    public function overviewTranslateAction($pid, $lang = null, Request $request) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $defaults = (array) $project->getLang($lang);
        $languages = Lang::listAll('name', false);

        // Create the form
        $processor = $this->getModelForm('ProjectTranslateOverview', $project, $defaults, ['lang' => $lang], $request);
        $processor->createForm();
        $processor->getBuilder()
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
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // Check if we want to remove a translation
            if($form->isValid() && $form->get('remove')->isClicked()) {
                if($project->removeLang($lang)) {
                    Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                } else {
                    Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                }
                return $this->redirect('/dashboard/project/' . $project->id . '/translate');
            }

            try {
                $processor->save($form);
                Message::info(Text::get('dashboard-translate-project-ok', [
                    '%ZONE%' => '<strong>' . Text::get('step-main') . '</strong>',
                    '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                ]));
                return $this->redirect('/dashboard/project/' . $project->id . '/translate');
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }


        return $this->viewResponse('dashboard/project/translate/overview', [
            'form' => $form->createView(),
            'step' => 'overview',
            'lang' => $lang,
        ]);
    }

    /**
     * Project costs translator
     */
    public function costsTranslateAction($pid, $lang = null, Request $request) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        // $langs = Lang::listAll('name', false);
        // $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        // if(!isset($languages[$lang])) {
        //     Message::error(Text::get('translator-lang-not-found'));
        //     return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        // }

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $costs = [];
        foreach($project->costs as $cost) {
            $suffix = "_{$cost->id}";
            $costs[$cost->id] = $cost;
            $translated = $cost->getLang($lang);
            $builder
                ->add("cost$suffix", 'text', [
                    'label' => 'costs-field-cost',
                    'data' => $translated->cost,
                    'required' => false,
                    'attr' => ['help' => $cost->cost]
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'costs-field-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $cost->description]
                ]);
        }
        $builder
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

        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $errors = [];
                $data = $form->getData();
                // print_r($data);die($form->getClickedButton()->getName());
                $errors = [];
                foreach($data as $key => $val) {
                    list($field, $id) = explode('_', $key);
                    $cost = $costs[$id];
                    // Check if we want to remove a translation
                    if($form->get('remove')->isClicked()) {
                        if(!$cost->removeLang($lang)) {
                            $errors[] = "Cost #$cost->id not deleted";
                        }
                    } else {
                        $cost->setLang($lang, [$field => $val], $errors);
                    }
                }
                if($errors) {
                    // print_r($errors);die;
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    } else {
                        Message::error(Text::get('form-sent-error', implode(',',$errors)));
                    }
                } else {
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('dashboard-translate-project-ok', [
                            '%ZONE%' => '<strong>' . Text::get('step-4') . '</strong>',
                            '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                        ]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/translate');
                }
            }
        }

        return $this->viewResponse('dashboard/project/translate/costs', [
            'form' => $form->createView(),
            'step' => 'costs',
            'costs' => $project->costs,
            'lang' => $lang,
            'types' => Cost::types(),
            'languages' => $languages,
        ]);

    }

    /**
     * Project rewards translator
     */
    public function rewardsTranslateAction($pid, $lang = null, Request $request) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        // $langs = Lang::listAll('name', false);
        // $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        // if(!isset($languages[$lang])) {
        //     Message::error(Text::get('translator-lang-not-found'));
        //     return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        // }

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $rewards = [];
        foreach($project->individual_rewards as $reward) {
            $suffix = "_{$reward->id}";
            $rewards[$reward->id] = $reward;
            $translated = $reward->getLang($lang);
            $builder
                ->add("reward$suffix", 'text', [
                    'label' => 'rewards-field-individual_reward-reward',
                    'data' => $translated->reward,
                    'required' => false,
                    'attr' => ['help' => $reward->reward]
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'rewards-field-individual_reward-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $reward->description]
                ]);
        }
        foreach($project->social_rewards as $reward) {
            $suffix = "_{$reward->id}";
            $rewards[$reward->id] = $reward;
            $translated = $reward->getLang($lang);
            $builder
                ->add("reward$suffix", 'text', [
                    'label' => 'rewards-field-social_reward-reward',
                    'data' => $translated->reward,
                    'required' => false,
                    'attr' => ['help' => $reward->reward]
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'rewards-field-social_reward-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $reward->description]
                ]);
        }
        $builder
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

        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $errors = [];
                $data = $form->getData();
                // print_r($data);die($form->getClickedButton()->getName());
                $errors = [];
                foreach($data as $key => $val) {
                    list($field, $id) = explode('_', $key);
                    $reward = $rewards[$id];
                    // Check if we want to remove a translation
                    if($form->get('remove')->isClicked()) {
                        if(!$reward->removeLang($lang)) {
                            $errors[] = "Reward #$reward->id not deleted";
                        }
                    } else {
                        $reward->setLang($lang, [$field => $val], $errors);
                    }
                }
                if($errors) {
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    } else {
                        Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                    }
                } else {
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('dashboard-translate-project-ok', [
                            '%ZONE%' => '<strong>' . Text::get('step-5') . '</strong>',
                            '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                        ]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/translate');
                }
            }
        }

        return $this->viewResponse('dashboard/project/translate/rewards', [
            'form' => $form->createView(),
            'step' => 'rewards',
            'rewards' => $project->individual_rewards,
            'social' => $project->social_rewards,
            'lang' => $lang,
            'languages' => $languages,
        ]);

    }

    /**
     * Project supports translator
     */
    public function supportsTranslateAction($pid, $lang = null, Request $request) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        // $langs = Lang::listAll('name', false);
        // $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        // if(!isset($languages[$lang])) {
        //     Message::error(Text::get('translator-lang-not-found'));
        //     return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        // }

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $supports = [];
        foreach($project->supports as $support) {
            $suffix = "_{$support->id}";
            $supports[$support->id] = $support;
            $translated = $support->getLang($lang);
            $builder
                ->add("support$suffix", 'text', [
                    'label' => 'supports-field-support',
                    'data' => $translated->support,
                    'required' => false,
                    'attr' => ['help' => $support->support]
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'supports-field-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $support->description]
                ]);
        }
        $builder
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

        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $errors = [];
                $data = $form->getData();
                // print_r($data);die($form->getClickedButton()->getName());
                $errors = [];
                foreach($data as $key => $val) {
                    list($field, $id) = explode('_', $key);
                    $support = $supports[$id];
                    // Check if we want to remove a translation
                    if($form->get('remove')->isClicked()) {
                        if(!$support->removeLang($lang)) {
                            $errors[] = "Reward #$support->id not deleted";
                        }
                    } else {
                        $support->setLang($lang, [$field => $val], $errors);
                    }
                }
                if($errors) {
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    } else {
                        Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                    }
                } else {
                    if($form->get('remove')->isClicked()) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('dashboard-translate-project-ok', [
                            '%ZONE%' => '<strong>' . Text::get('step-6') . '</strong>',
                            '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                        ]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/translate');
                }
            }
        }

        return $this->viewResponse('dashboard/project/translate/supports', [
            'form' => $form->createView(),
            'step' => 'supports',
            'supports' => $project->supports,
            'lang' => $lang,
            'languages' => $languages,
        ]);

    }

    /**
     * Project updates translator
     */
    public function updatesTranslateAction($pid, $uid, $lang, Request $request)
    {
        $project = $this->validateProject($pid, 'updates', null, $form, $lang);
        if($project instanceOf Response) return $project;

        $post = BlogPost::get($uid);
        if(!$post instanceOf BlogPost) throw new ModelException("Post [$uid] not found for project [{$project->id}]");

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        $languages[$project->lang] = $langs[$project->lang];

        if(!isset($languages[$lang])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/project/' . $project->id . '/updates/' . $uid);
        }

        $defaults = (array) $post->getLang($lang);
        // Create the form
        $form = $this->createFormBuilder($defaults)
            ->add('title', 'text', array(
                'label' => 'regular-title',
                'required' => false,
                'attr' => ['help' => $post->title],
            ))
            ->add('text', 'markdown', array(
                'label' => 'regular-text',
                'required' => false,
                'attr' => ['help' => $post->text, 'rows' => 10]
            ))
            ->add('submit', 'submit', array(
                // 'icon_class' => null
            ))
            ->add('remove', 'submit', [
                'label' => Text::get('translator-delete', $languages[$lang]),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right-form btn btn-default btn-lg',
                    'data-confirm' => Text::get('translator-delete-sure', $languages[$lang])
                    ]
            ])
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                // Check if we want to remove a translation
                if($form->get('remove')->isClicked()) {
                    if($post->removeLang($lang)) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/updates');
                }

                $data = $form->getData();
                // var_dump($data);die;
                $post->lang = $lang;
                $post->title_lang = $data['title'];
                $post->text_lang = $data['text'];
                if($post->saveLang($errors)) {
                    Message::info(Text::get('dashboard-project-updates-translate-ok', [
                        '%TITLE%' => '<strong>#' . $post->id .'</strong>',
                        '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                    ]));
                    return $this->redirect('/dashboard/project/' . $project->id . '/updates');
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }

            } else {
                Message::error(Text::get('form-has-errors'));
            }
        }
        return $this->viewResponse('dashboard/project/updates_edit', [
            'post' => $post,
            'form' => $form->createView(),
            'lang' => $lang,
            'languages' => $languages,
            'translated' => $post->getLangsAvailable(),
            'skip' => $project->lang,
            'exit_link' => '/dashboard/project/' . $project->id . '/updates/' . $uid
            ]);

    }

}
