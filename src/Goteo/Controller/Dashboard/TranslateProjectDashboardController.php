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
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Support;
use Goteo\Model\Project\Cost;
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

class TranslateProjectDashboardController extends \Goteo\Controller\Dashboard\ProjectDashboardController {

    /**
     * Additional common context vars for the views
     */
    public function validateProject($pid = null, $section = 'summary', $lang = null, $lang_check = null) {
        $project = parent::validateProject($pid, $section, $lang);
        if(!$project instanceOf Project) return $project;

        $languages = Lang::listAll('name', false);
        if($lang_check && !isset($languages[$lang_check])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        }

        View::getEngine()->useData([
            'zones' => [
                // 'profile' => Text::get('step-1'),
                'overview' => Text::get('step-main'),
                'costs' => Text::get('step-4'),
                'rewards' => Text::get('step-5'),
                // 'supports' => Text::get('step-6'),
                // 'updates' => Text::get('project-menu-updates')
            ],
            'languages' => $languages,
            'translated' => array_diff($project->getLangsAvailable(), [$project->lang])

        ]);
        return $project;
    }

    /**
     * Some handy defaults for the form
     */
    public function createFormBuilder($defaults = null, $name = 'autoform', array $options = ['attr' => ['class' => 'autoform hide-help']]) {
        return parent::createFormBuilder($defaults, $name, $options);
    }

    /**
     * Index translator
     */
    public function translateAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'translate');
        if($project instanceOf Response) return $project;

        return $this->viewResponse('dashboard/project/translate/index', [
            'zones' => null // no tabs here
        ]);

    }

    /**
     * Project overview translator
     */
    public function overviewTranslateAction($pid, $lang = null, Request $request) {

        $project = $this->validateProject($pid, 'translate', null, $lang); // original lang
        if($project instanceOf Response) return $project;

        $defaults = (array) $project->getLang($lang);

        $builder = $this->createFormBuilder($defaults)
            ->add('subtitle', 'text', [
                'label' => 'overview-field-subtitle',
                'required' => false,
                'attr' => ['help' => $project->subtitle]
            ])
            ->add('description', 'textarea', [
                'label' => 'overview-field-description',
                'required' => false,
                'attr' => ['help' => $project->description, 'rows' => 10]
            ])
            ->add('media', 'media', [
                'label' => 'overview-field-media',
                'required' => false,
                'attr' => ['help' => $project->media]
            ])
            ->add('motivation', 'textarea', [
                'label' => 'overview-field-motivation',
                'required' => false,
                'attr' => ['help' => $project->motivation, 'rows' => 10]
            ])
            ->add('video', 'media', [
                'label' => 'overview-field-video',
                'required' => false,
                'attr' => ['help' => $project->video]
            ])
            ->add('about', 'textarea', [
                'label' => 'overview-field-about',
                'required' => false,
                'attr' => ['help' => $project->about, 'rows' => 10]
            ])
            ->add('goal', 'textarea', [
                'label' => 'overview-field-goal',
                'required' => false,
                'attr' => ['help' => $project->goal, 'rows' => 10]
            ])
            ->add('related', 'textarea', [
                'label' => 'overview-field-related',
                'required' => false,
                'attr' => ['help' => $project->related, 'rows' => 10]
            ])
            // ->add('keywords', 'tags', [
            //     'label' => 'overview-field-keywords',
            //     'required' => false,
            //     'attr' => ['help' => $project->keywords]
            // ])
            ->add('social_commitment_description', 'textarea', [
                'label' => 'overview-field-social-description',
                'required' => false,
                'attr' => ['help' => $project->social_commitment_description, 'rows' => 10]
            ])
            ->add('submit', 'submit')
            ->add('remove', 'submit', [
                'label' => Text::get('translator-delete', $languages[$lang]),
                'icon_class' => 'fa fa-trash',
                'attr' => [
                    'class' => 'pull-right-form hide-form btn btn-danger btn-lg',
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
                $project->lang = $lang;
                // Check if we want to remove a translation
                if($form->get('remove')->isClicked()) {
                    if($project->removeLang($lang)) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/translate');
                }

                $data = $form->getData();
                $project->lang_lang = $lang;
                $project->subtitle_lang = $data['subtitle'];
                $project->description_lang = $data['description'];
                $project->media_lang = $data['media'];
                $project->motivation_lang = $data['motivation'];
                $project->video_lang = $data['video'];
                $project->about_lang = $data['about'];
                $project->goal_lang = $data['goal'];
                $project->related_lang = $data['related'];
                $project->social_commitment_description_lang = $data['social_commitment_description'];
                // $project->keywords_lang = $data['keywords'];
                $project->keywords_lang = $project->keywords; // Do not translate keywords for the moment
                $project->contribution_lang = $data['contribution'];
                if($project->saveLang($errors)) {
                    Message::info(Text::get('dashboard-translate-project-ok', [
                        '%ZONE%' => '<strong>' . Text::get('step-3') . '</strong>',
                        '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                    ]));
                    return $this->redirect('/dashboard/project/' . $project->id . '/translate');
                } else {
                    Message::error(Text::get('form-sent-error', implode(',',array_map('implode',$errors))));
                }
            } else {
                Message::error(Text::get('form-has-errors'));
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

        $project = $this->validateProject($pid, 'translate', null, $lang); // original lang
        if($project instanceOf Response) return $project;

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        if(!isset($languages[$lang])) {
            Message::error(Text::get('translator-lang-not-found'));
            return $this->redirect('/dashboard/project/' . $project->id . '/translate');
        }

        $builder = $this->createFormBuilder();
        $costs = [];
        foreach($project->costs as $cost) {
            $suffix = "_{$cost->id}";
            $costs[$cost->id] = $cost;
            $builder
                ->add("cost$suffix", 'text', [
                    'label' => 'costs-field-cost',
                    'required' => false,
                ])
                ->add("description$suffix", 'textarea', [
                    'label' => 'costs-field-description',
                    'required' => false,
                ]);
        }
        $builder
            ->add('submit', 'submit')
            ->add('remove', 'submit', [
                'label' => Text::get('translator-delete', $languages[$lang]),
                'icon_class' => 'fa fa-trash',
                'attr' => [
                    'class' => 'pull-right-form hide-form btn btn-danger btn-lg',
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
                    $cost->lang = $lang;
                    $cost->project = $project->id;
                    $cost->{$field . '_lang'} = $val;
                    // Check if we want to remove a translation
                    if($form->get('remove')->isClicked()) {
                        if(!$cost->removeLang($lang)) {
                            $errors[] = "Cost #$cost->id not deleted";
                        }
                    } else {
                        $cost->saveLang($errors);
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
            'costs' => $this->project->costs,
            'lang' => $lang,
            'types' => Cost::types(),
            'languages' => $languages,
        ]);

    }

    /**
     * Project updates translator
     */
    public function updatesTranslateAction($pid, $uid, $lang, Request $request)
    {
        $project = $this->validateProject($pid, 'updates', null, $lang);
        if($project instanceOf Response) return $project;

        $post = BlogPost::get($uid);
        if(!$post instanceOf BlogPost) throw new ModelException("Post [$uid] not found for project [{$project->id}]");

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));

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
                'attr' => [
                    'class' => 'pull-right-form hide-form btn btn-danger btn-lg',
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
                $post->text_lang = $data['title'];
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
