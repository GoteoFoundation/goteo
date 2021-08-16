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
use Goteo\Library\Forms\Admin\AdminWorkshopEditForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Workshop;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class WorkshopAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-graduation-cap"></i>';

	public static function getGroup(): string
    {
		return 'services';
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
		$filters = [];
        $limit = 20;
		$page = $request->query->get('pag') ?: 0;
		$list = Workshop::getList($filters, $page * $limit, $limit, false, Config::get('lang'));
		$total = Workshop::getList($filters, 0, 0, true);

		return $this->viewResponse('admin/workshop/list', [
			'list' => $list,
			'total' => $total,
			'limit' => $limit
		]);
	}

	public function editAction(Request $request, $id = '') {

		$workshop = $id ? Workshop::get($id) : new workshop();

		if (!$workshop) {
			throw new ModelNotFoundException("Not found workshop [$id]");
		}

		$defaults = (array) $workshop;
		$processor = $this->getModelForm(AdminWorkshopEditForm::class, $workshop, $defaults, [], $request);
		$processor->createForm();
		$processor->getBuilder()
			->add('submit', SubmitType::class, [
				'label' => 'regular-submit',
			]);
		if ($workshop->id) {
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
				$workshop->dbDelete(); //Throws and exception if fails
				Message::info(Text::get('admin-remove-entry-ok'));
				return $this->redirect('/admin/workshop');
			}

			try {
				$processor->save($form); // Allow save event if does not validate
				Message::info(Text::get('admin-workshop-edit-success'));
				return $this->redirect('/admin/workshop?' . $request->getQueryString());
			} catch (FormModelException $e) {
				Message::error($e->getMessage());
			}
		}

		return $this->viewResponse('admin/workshop/edit', [
			'form' => $form->createView(),
			'workshop' => $workshop,
			'user' => $user,
		]);
	}
}
