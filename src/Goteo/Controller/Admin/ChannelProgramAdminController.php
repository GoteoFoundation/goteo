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
use Goteo\Library\Forms\Admin\AdminProgramForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeProgram;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class ChannelProgramAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-calendar-check-o"></i>';

    public static function getGroup(): string
    {
        return 'channels';
    }

  public static function getRoutes(): array
  {
    return [
      new Route(
        '/',
        ['_controller' => function () {
            return new RedirectResponse("/admin/channelprogram/" . Config::get('node'));
        }]
      ),
      new Route(
        '/{id}',
        ['_controller' => __CLASS__ . "::listAction"]
      ),
      new Route(
        '/{node}/add',
        ['_controller' => __CLASS__ . "::addAction"]
      ),
      new Route(
        '/{node}/edit/{program_id}',
        ['_controller' => __CLASS__ . "::editAction"]
      )
      ];
  }

  public function listAction($id, Request $request) {
    try {
        $channel = Node::get($id);
    } catch (ModelNotFoundException $e) {
        Message::error($e->getMessage());
        return $this->redirect('/admin');
    }

    $page = $request->query->get('pag') ?: 0;
    $limit = 10;

    $list = NodeProgram::getList(['node' => $id], $page * $limit, $limit, false);
    $total = NodeProgram::getList(['node' => $id], 0, 0, true);

    return $this->viewResponse('admin/channelprogram/list', [
      'current_node' => $id,
      'nodes' => $this->user->getNodeNames(),
      'program' => $program,
      'total' => $total,
      'list' => $list
    ]);
  }

  public function addAction($node, Request $request) {
    try {
        Node::get($node);
    } catch (ModelNotFoundException $e) {
        Message::error($e->getMessage());
        return $this->redirect('/admin/channelprogram');
    }

    $program = new NodeProgram();
    $program->node_id = $node;

    $processor = $this->getModelForm(AdminProgramForm::class, $program, [], [], $request);
    $processor->createForm()->getBuilder()
      ->add('submit', SubmitType::class, [
        'label' => 'admin-channelprogram-create',
        'attr' => ['class' => 'btn btn-cyan'],
        'icon_class' => 'fa fa-save'
    ]);

    $form = $processor->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $request->isMethod('post')) {
      try {
        $processor->save($form);
        Message::info(Text::get('admin-edit-add-ok'));
        return $this->redirect("/admin/channelprogram/" . $node);

      } catch (FormModelException $e) {
        Message::error(Text::get('form-has-errors'));
      }
    }

    return $this->viewResponse('admin/channelprogram/edit', [
      'current_node' => $node,
      'form' => $form->createView()
    ]);
  }

  public function editAction($node, $program_id, Request $request) {
    try {
        Node::get($node);
    } catch (ModelNotFoundException $e) {
        Message::error($e->getMessage());
        return $this->redirect('/admin/channelprogram');
    }

    $program = NodeProgram::get($program_id);

    $processor = $this->getModelForm(AdminProgramForm::class, $program, (array) $program, [], $request);
    $processor->createForm()->getBuilder()
      ->add('submit', SubmitType::class, [
        'label' => 'regular-submit',
        'attr' => ['class' => 'btn btn-cyan'],
        'icon_class' => 'fa fa-save'
    ]);

    $form = $processor->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $request->isMethod('post')) {
      try {
        $processor->save($form);
        Message::info(Text::get('admin-edit-entry-ok'));
        return $this->redirect("/admin/channelprogram/" . $node);

      } catch (FormModelException $e) {
        Message::error(Text::get('form-has-errors'));
      }
    }

    return $this->viewResponse('admin/channelprogram/edit', [
      'current_node' => $node,
      'program' => $program,
      'form' => $form->createView()
    ]);
  }
}
