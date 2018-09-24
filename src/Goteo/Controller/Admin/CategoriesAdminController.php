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
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Category;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Sphere;
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * This module should admin Categories, Spheres, SocialCommitments, SDGs, Footprints
 * and its interelationships
 */
class CategoriesAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-object-group"></i>';
    protected static $label = 'admin-categories';

	// this modules is part of a specific group
	public static function getGroup() {
		return 'contents';
	}

	public static function getRoutes() {
		return [
			new Route(
				'/{tab}',
				['_controller' => __CLASS__ . "::listAction", 'tab' => 'category']
			),
			new Route(
				'/edit/{id}', [
					'_controller' => __CLASS__ . "::editAction",
				]
			),
			new Route(
				'/add',
				['_controller' => __CLASS__ . "::editAction", 'id' => '']
			),
		];
	}

	public function listAction($tab = 'category', Request $request) {
        $tabs = ['category' => 'categories', 'social' => 'social_commitments', 'sphere' => 'spheres', 'sdg' => 'sdgs', 'footprint' => 'footprints'];
        if(!isset($tabs[$tab])) throw new ModelNotFoundException("Not found type [$tab]");


        if($tab === 'social') {
            $list = SocialCommitment::getAll(Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'langs', 'order', 'actions'];
        } elseif($tab === 'sphere') {
            $list = Sphere::getAll([], Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'landing_match', 'langs', 'order', 'actions'];
        } elseif($tab === 'sdg') {
            $list = Sdg::getList([],0,100, false, Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'langs', 'order', 'actions'];
        } elseif($tab === 'footprint') {
            $list = Footprint::getList([],0,100, false, Config::get('lang'));
            $fields = ['id', 'icon', 'name', 'langs', 'order', 'actions'];
        } else {
            $list = Category::getAll(Config::get('lang'));
            $fields = ['id', 'name', 'social_commitment', 'langs', 'order', 'actions'];
        }

		return $this->viewResponse('admin/categories/list', [
            'tab' => $tab,
            'tabs' => $tabs,
            'list' => $list,
			'fields' => $fields,
			'link_prefix' => '/categories/edit/',
		]);
	}

	public function editAction($id = '', Request $request) {

		$story = $id ? Category::get($id) : new Category();

		if (!$story) {
			throw new ModelNotFoundException("Not found story [$id]");
		}

		$defaults = (array) $story;
		$processor = $this->getModelForm('AdminStoryEdit', $story, $defaults, [], $request);
		$processor->createForm();
		$processor->getBuilder()
			->add('submit', 'submit', [
				'label' => $submit_label ? $submit_label : 'regular-submit',
			]);
		if ($story->id) {
			$processor->getBuilder()
				->add('remove', 'submit', [
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
				if ((bool) $story->active) {
					Message::error(Text::get('admin-remove-entry-forbidden'));
					return $this->redirect('/admin/stories');
				}

				$story->dbDelete(); //Throws and exception if fails
				Message::info(Text::get('admin-remove-entry-ok'));
				return $this->redirect('/admin/stories');
			}

			try {
				$processor->save($form); // Allow save event if does not validate
				Message::info(Text::get('admin-stories-edit-success'));
				return $this->redirect('/admin/stories?' . $request->getQueryString());
			} catch (FormModelException $e) {
				Message::error($e->getMessage());
			}
		}

		return $this->viewResponse('admin/stories/edit', [
			'form' => $form->createView(),
			'story' => $story,
			'user' => $user,
		]);
	}
}
