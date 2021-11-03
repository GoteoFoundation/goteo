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
use Goteo\Library\Forms\Admin\AdminChannelResourceEditForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Node\NodeResource as Resource;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class ChannelResourceAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-download"></i>';

	public static function getGroup(): string
    {
		return 'channels';
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
		$list = Resource::getList($filters, $page * $limit, $limit, false, Config::get('lang'));
		$total = count($list);

		return $this->viewResponse('admin/channel/resource/list', [
			'list' => $list,
			'total' => $total,
			'limit' => $limit
		]);
	}

	public function editAction(Request $request, $id = '') {

		$resource = $id ? Resource::get($id) : new Resource();

		if (!$resource) {
			throw new ModelNotFoundException("Not found resource [$id]");
		}

		$defaults = (array) $resource;
		$processor = $this->getModelForm(AdminChannelResourceEditForm::class, $resource, $defaults, [], $request);
		$processor->createForm();
		$processor->getBuilder()
			->add('submit', SubmitType::class, [
				'label' => 'regular-submit',
			]);
		if ($resource->id) {
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
				$resource->dbDelete(); //Throws and exception if fails
				Message::info(Text::get('admin-remove-entry-ok'));
				return $this->redirect('/admin/channelresource');
			}

			try {
				$processor->save($form); // Allow save event if does not validate
				Message::info(Text::get('admin-resource-edit-success'));
				return $this->redirect('/admin/channelresource?' . $request->getQueryString());
			} catch (FormModelException $e) {
				Message::error($e->getMessage());
			}
		}

		return $this->viewResponse('admin/channel/resource/edit', [
			'form' => $form->createView(),
			'resource' => $resource,
			'user' => $user,
		]);
	}
}
