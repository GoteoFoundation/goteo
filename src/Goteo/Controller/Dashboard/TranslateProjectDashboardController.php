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

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\ProjectTranslateOverviewForm;
use Goteo\Library\Forms\Model\ProjectTranslateStoryForm;
use Goteo\Library\Text;
use Goteo\Model\Message as Comment;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\Project;
use Goteo\Model\Project\Cost;
use Goteo\Model\Stories;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TextareaType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        if($project->isFunded()){
            $data['zones']['story']=Text::get('dashboard-story-translate-title');
        }

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

            if($project->isFunded()){
                $story = reset(Stories::getall(false, false, ['project' => $this->project->id]));
                $data['percents']['story']= $story ? $story->getLangsGroupPercent($lang_check, ['id']) : 0;
            }
        }

        View::getEngine()->useData($data);
        return $project;
    }

    public function createFormBuilder(
        $defaults = null,
        $name = 'autoform',
        array $options = ['attr' => ['class' => 'autoform hide-help']]
    ): FormBuilder {
        return parent::createFormBuilder($defaults, $name, $options);
    }

    /**
     * Index translator
     */
    public function translateAction($pid) {
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

            if($project->isFunded()){
                $story = reset(Stories::getall(false, false, ['project' => $this->project->id]));
                $percents[$lang]['story']= $story ? $story->getLangsGroupPercent($lang, ['id']) : 0;

            }
        }

        return $this->viewResponse('dashboard/project/translate/index', [
            'translated' => $translated,
            'percents' => $percents
        ]);
    }

    /**
     * Project overview translator
     */
    public function overviewTranslateAction(Request $request, $pid, $lang = null) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $defaults = (array) $project->getLang($lang);
        $languages = Lang::listAll('name', false);

        $processor = $this->getModelForm(
            ProjectTranslateOverviewForm::class,
            $project,
            $defaults,
            ['lang' => $lang],
            $request,
            ['csrf_protection' => false, 'attr' => ['class' => 'autoform hide-help']]
        );
        $processor->createForm();
        $processor->getBuilder()
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
    public function costsTranslateAction(Request $request, $pid, $lang = null) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $costs = [];
        foreach($project->costs as $cost) {
            $suffix = "_{$cost->id}";
            $costs[$cost->id] = $cost;
            $translated = $cost->getLang($lang);
            $builder
                ->add("cost$suffix", TextType::class, [
                    'label' => 'costs-field-cost',
                    'data' => $translated->cost,
                    'required' => false,
                    'attr' => ['help' => $cost->cost]
                ])
                ->add("description$suffix", TextareaType::class, [
                    'label' => 'costs-field-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $cost->description]
                ]);
        }
        $builder
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
                $data = $form->getData();
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
    public function rewardsTranslateAction(Request $request, $pid, $lang = null) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $rewards = [];
        foreach($project->individual_rewards as $reward) {
            $suffix = "_{$reward->id}";
            $rewards[$reward->id] = $reward;
            $translated = $reward->getLang($lang);
            $builder
                ->add("reward$suffix", TextType::class, [
                    'label' => 'rewards-field-individual_reward-reward',
                    'data' => $translated->reward,
                    'required' => false,
                    'attr' => ['help' => $reward->reward]
                ])
                ->add("description$suffix", MarkdownType::class, [
                    'label' => 'rewards-field-individual_reward-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => [
                        'help' => $reward->description,
                        'data-image-upload' => '/api/projects/' . $project->id . '/images',
                        'rows' => 4,
                        'data-toolbar' => 'close,bold,italic,link,unordered-list,ordered-list,preview,fullscreen,guide'
                    ]
                ]);
        }
        foreach($project->social_rewards as $reward) {
            $suffix = "_{$reward->id}";
            $rewards[$reward->id] = $reward;
            $translated = $reward->getLang($lang);
            $builder
                ->add("reward$suffix", TextType::class, [
                    'label' => 'rewards-field-social_reward-reward',
                    'data' => $translated->reward,
                    'required' => false,
                    'attr' => ['help' => $reward->reward]
                ])
                ->add("description$suffix", MarkdownType::class, [
                    'label' => 'rewards-field-social_reward-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => [
                        'help' => $reward->description,
                        'data-image-upload' => '/api/projects/' . $project->id . '/images',
                        'rows' => 4,
                        'data-toolbar' => 'close,bold,italic,link,unordered-list,ordered-list,preview,fullscreen,guide'
                    ]
                ]);
        }
        $builder
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
                $data = $form->getData();
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
    public function supportsTranslateAction(Request $request, $pid, $lang = null) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $languages = Lang::listAll('name', false);

        $builder = $this->createFormBuilder();
        $supports = [];
        foreach($project->supports as $support) {
            $suffix = "_{$support->id}";
            $supports[$support->id] = $support;
            $translated = $support->getLang($lang);
            $builder
                ->add("support$suffix", TextType::class, [
                    'label' => 'supports-field-support',
                    'data' => $translated->support,
                    'required' => false,
                    'attr' => ['help' => $support->support]
                ])
                ->add("description$suffix", TextareaType::class, [
                    'label' => 'supports-field-description',
                    'data' => $translated->description,
                    'required' => false,
                    'attr' => ['help' => $support->description]
                ]);
        }
        $builder
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
                $data = $form->getData();
                $errors = [];
                $removeTranslation = $form->get('remove')->isClicked();

                foreach($supports as $support) {
                    if ($removeTranslation) {
                        if(!$support->removeLang($lang)) {
                            $errors[] = "Reward #$support->id not deleted";
                        } else {
                            $msg = Comment::get($support->thread);
                            if (!$msg->removeLang($lang))
                                $errors[] = "Comment #$msg->id not deleted";
                        }
                    } else {
                        $translatedSupport = $data["support_{$support->id}"];
                        $translatedDescription = $data["description_{$support->id}"];

                        $support->setLang($lang, ["support" => $translatedSupport, "description" => $translatedDescription], $errors);

                        $msg = Comment::get($support->thread);
                        $translatedMessage = "{$translatedSupport}: {$translatedDescription}";
                        $msg->setLang($lang, ['message' => $translatedMessage], $errors);
                    }
                }

                if($errors) {
                    if($removeTranslation) {
                        Message::error(Text::get('translator-deleted-ko', $languages[$lang]));
                    } else {
                        Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                    }
                } else {
                    if($removeTranslation) {
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

        $post = BlogPost::getBySlug($uid);
        if(!$post instanceOf BlogPost) {
            throw new ModelException("Post [$uid] not found for project [{$project->id}]");
        }

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        $languages[$project->lang] = $langs[$project->lang];

        if(!isset($languages[$lang])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/project/' . $project->id . '/updates/' . $uid);
        }

        $defaults = (array) $post->getLang($lang);
        $form = $this->createFormBuilder($defaults)
            ->add('title', TextType::class, array(
                'label' => 'regular-title',
                'required' => false,
                'attr' => ['help' => $post->title],
            ))
            ->add('text', MarkdownType::class, array(
                'label' => 'regular-text',
                'required' => false,
                'attr' => ['help' => $post->text, 'rows' => 10]
            ))
            ->add('submit', SubmitType::class, [])
            ->add('remove', SubmitType::class, [
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
                $data['blog'] = $post->blog;
                $errors = [];

                // Remove html tags if has no permission
                if(!Session::getUser()->hasPerm('full-html-edit')) {
                    $data['text'] = Text::tags_filter($data['text']);
                }

                if($post->setLang($lang, $data, $errors)) {
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
            ]
        );
    }

    /**
     * Project story translator
    */
    public function storyTranslateAction(Request $request, $pid, $lang = null) {

        $project = $this->validateProject($pid, 'translate', null, $form, $lang); // original lang
        if($project instanceOf Response) return $project;

        $languages = Lang::listAll('name', false);

        $story = reset(Stories::getList(['project' => $project->id],0,1,false, $lang));

        if(!$story)
            return $this->redirect('/dashboard/project/' . $project->id . '/story');

        $defaults = (array) $story->getLang($lang);

        $processor = $this->getModelForm(
            ProjectTranslateStoryForm::class,
            $story,
            $defaults,
            ['lang' => $lang],
            $request,
            ['csrf_protection' => false, 'attr' => ['class' => 'autoform hide-help']]
        );
        $processor->createForm();
        $processor->getBuilder()
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
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // Check if we want to remove a translation
            if($form->isValid() && $form->get('remove')->isClicked()) {
                if($story->removeLang($lang)) {
                    Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                } else {
                    Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                }
                return $this->redirect('/dashboard/project/' . $project->id . '/translate');
            }

            try {
                $processor->save($form);
                Message::info(Text::get('dashboard-translate-project-ok', [
                    '%ZONE%' => '<strong>' . Text::get('dashboard-story-translate-title') . '</strong>',
                    '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                ]));
                return $this->redirect('/dashboard/project/' . $project->id . '/translate');
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('dashboard/project/translate/story', [
            'form' => $form->createView(),
            'step' => 'story',
            'lang' => $lang,
        ]);
    }

}
