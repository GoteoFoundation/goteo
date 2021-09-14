<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Library\Forms\Admin\AdminCategoryEditForm;
use Goteo\Library\Forms\Admin\AdminFootprintEditForm;
use Goteo\Library\Forms\Admin\AdminSdgEditForm;
use Goteo\Library\Forms\Admin\AdminSocialCommitmentEditForm;
use Goteo\Library\Forms\Admin\AdminSphereEditForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Category;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Sphere;
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * This module should admin Categories, Spheres, SocialCommitments, SDGs, Footprints
 * and its interrelationships
 */
class CategoriesAdminController extends AbstractAdminController
{
    const TAB_PROPERTY_TEXT = "text";
    const TAB_PROPERTY_MODEL = "model";
    const TAB_PROPERTY_MODEL_CLASS = "modelClass";
    const TAB_PROPERTY_FORM_CLASS = "form";

	protected static $icon = '<i class="fa fa-2x fa-object-group"></i>';
    protected static $label = 'admin-categories';

    protected $tabs = [
        'category' => [
            self::TAB_PROPERTY_TEXT => 'categories',
            self::TAB_PROPERTY_MODEL => 'Category',
            self::TAB_PROPERTY_MODEL_CLASS => Category::class,
            self::TAB_PROPERTY_FORM_CLASS => AdminCategoryEditForm::class
        ],
        'socialcommitment' => [
            self::TAB_PROPERTY_TEXT => 'social_commitments',
            self::TAB_PROPERTY_MODEL => 'SocialCommitment',
            self::TAB_PROPERTY_MODEL_CLASS => SocialCommitment::class,
            self::TAB_PROPERTY_FORM_CLASS => AdminSocialCommitmentEditForm::class
        ],
        'sphere' => [
            self::TAB_PROPERTY_TEXT => 'spheres',
            self::TAB_PROPERTY_MODEL => 'Sphere',
            self::TAB_PROPERTY_MODEL_CLASS => Sphere::class,
            self::TAB_PROPERTY_FORM_CLASS => AdminSphereEditForm::class
        ],
        'sdg' => [
            self::TAB_PROPERTY_TEXT => 'sdgs',
            self::TAB_PROPERTY_MODEL => 'Sdg',
            self::TAB_PROPERTY_MODEL_CLASS => Sdg::class,
            self::TAB_PROPERTY_FORM_CLASS => AdminSdgEditForm::class
        ],
        'footprint' => [
            self::TAB_PROPERTY_TEXT => 'footprints',
            self::TAB_PROPERTY_MODEL => 'Footprint',
            self::TAB_PROPERTY_MODEL_CLASS => Footprint::class,
            self::TAB_PROPERTY_FORM_CLASS => AdminFootprintEditForm::class
        ]
    ];

	public static function getGroup(): string
    {
		return 'contents';
	}

	public static function getRoutes(): array
    {
		return [
			new Route(
				'/{tab}',
				['_controller' => __CLASS__ . "::listAction", 'tab' => 'category']
			),
			new Route(
				'/{tab}/edit/{id}',
                ['_controller' => __CLASS__ . "::editAction", 'tab' => 'category']
			),
			new Route(
				'/{tab}/add',
				['_controller' => __CLASS__ . "::editAction", 'tab' => 'category', 'id' => '']
			),
		];
	}

	public function listAction($tab = 'category') {
        if(!isset($this->tabs[$tab])) throw new ModelNotFoundException("Not found type [$tab]");

        if($tab === 'socialcommitment') {
            $list = SocialCommitment::getAll(Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'sdgs', 'footprints', 'langs', /*'order',*/ 'actions'];
        } elseif($tab === 'sphere') {
            $list = Sphere::getAll([], Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'landing_match', 'sdgs', 'footprints', 'langs', /*'order',*/ 'actions'];
        } elseif($tab === 'sdg') {
            $list = Sdg::getList([],0,100, false, Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'footprints', 'langs', 'actions'];
        } elseif($tab === 'footprint') {
            $list = Footprint::getList([],0,100, false, Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'sdgs', 'categories', 'social_commitments', 'langs', 'actions'];
        } else {
            $list = Category::getAll(Config::get('lang'));
            $fields = ['id', 'name', 'social_commitment', 'sdgs', 'footprints', 'langs', /*'order',*/ 'actions'];
        }

		return $this->viewResponse('admin/categories/list', [
            'tab' => $tab,
            'tabs' => $this->tabs,
            'list' => $list,
            'fields' => $fields,
            'link_prefix' => '/categories/edit/',
        ]);
    }

    public function editAction(Request $request, $tab = 'category', $id = '') {

        if(!isset($this->tabs[$tab])) throw new ModelNotFoundException("Not found type [$tab]");
        $model = $this->tabs[$tab][self::TAB_PROPERTY_MODEL];
        $modelClass = $this->tabs[$tab][self::TAB_PROPERTY_MODEL_CLASS];
        $formClass = $this->tabs[$tab][self::TAB_PROPERTY_FORM_CLASS];

        $instance = $id ? $modelClass::get($id, Config::get('sql_lang')) : new $modelClass();

        if (!$instance) {
            throw new ModelNotFoundException("Not found $model [$id]");
        }

        $defaults = (array) $instance;
        $processor = $this->getModelForm($formClass, $instance, $defaults, [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit',
            ]);
        if ($instance->id) {
            $processor->getBuilder()
                ->add('remove', SubmitType::class, [
                    'label' => Text::get('admin-remove-entry'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right-form btn btn-default btn-lg',
                        'data-confirm' => Text::get('admin-remove-entry-confirm'),
                    ],
                ]);
        }

        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            // Check if we want to remove an entry
            if ($form->has('remove') && $form->get('remove')->isClicked()) {
                try {
                    $instance->dbDelete(); //Throws and exception if fails
                } catch(\PDOException $e) {
                    Message::error($e->getMessage());
                }
                Message::info(Text::get('admin-remove-entry-ok'));
                return $this->redirect("/admin/categories/{$tab}");
            }

            try {
                $processor->save($form); // Allow save event if does not validate
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/admin/categories/{$tab}?" . $request->getQueryString());
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/categories/edit', [
            'tab' => $tab,
            'form' => $form->createView(),
            'instance' => $instance,
            'user' => $user,
        ]);
	}
}
