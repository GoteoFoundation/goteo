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
use Goteo\Library\Forms\Admin\AdminStoryEditForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Stories;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class StoriesAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-id-card-o"></i>';

	public static function getGroup(): string
    {
		return 'main';
	}

	public static function getRoutes(): array
    {
		return [
			new Route(
				'/',
				['_controller' => __CLASS__ . "::listAction"]
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

	public function listAction(Request $request) {
		$filters = ['superglobal' => $request->query->get('q')];
        $limit = 20;
		$page = $request->query->get('pag') ?: 0;
		$list = Stories::getList($filters, $page * $limit, $limit, false, Config::get('lang'));
		$total = Stories::getList($filters, 0, 0, true);

		return $this->viewResponse('admin/stories/list', [
			'list' => $list,
			'link_prefix' => '/stories/edit/',
			'total' => $total,
			'limit' => $limit,
			'filter' => [
				'_action' => '/stories',
				'q' => Text::get('admin-stories-global-search'),
			],
		]);
	}

	public function editAction(Request $request, $id = '') {

		$story = $id ? Stories::get($id) : new Stories();

		if (!$story) {
			throw new ModelNotFoundException("Not found story [$id]");
		}

		$defaults = (array) $story;
		$processor = $this->getModelForm(AdminStoryEditForm::class, $story, $defaults, [], $request);
		$processor->createForm();
		$processor->getBuilder()
			->add('submit', SubmitType::class, [
				'label' => 'regular-submit',
			]);
		if ($story->id) {
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
