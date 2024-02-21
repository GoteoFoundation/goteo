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
use Goteo\Library\Forms\Admin\AdminSectionForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeSections;
use Goteo\Util\Form\Type\SubmitType;
use PDOException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class ChannelSectionAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-tasks"></i>';

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
                    return new RedirectResponse("/admin/channelsection/" . Config::get('node'));
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
                '/{node}/edit/{section_id}',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/{node}/delete/{section_id}',
                ['_controller' => __CLASS__ . "::deleteAction"]
            )
        ];
    }

    public function listAction($id, Request $request)
    {
        try {
            $channel = Node::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin');
        }

        $page = $request->query->get('pag') ?: 0;
        $limit = 10;

        $list = NodeSections::getList(['node' => $id], $page * $limit, $limit, false);
        $total = NodeSections::getList(['node' => $id], 0, 0, true);

        return $this->viewResponse('admin/channelsection/list', [
            'current_node' => $id,
            'nodes' => $this->user->getNodeNames(),
            'program' => $section,
            'total' => $total,
            'list' => $list
        ]);
    }

    public function addAction($node, Request $request)
    {
        try {
            $channel = Node::get($node);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/channelsection');
        }

        $section = new NodeSections();
        $section->node = $node;
        $order = NodeSections::next($node);
        if ($order)
            $section->order = NodeSections::next($node);

        $processor = $this->getModelForm(AdminSectionForm::class, $section, [], [], $request);
        $processor->createForm()->getBuilder()
            ->add('submit', SubmitType::class, [
                'label' => 'admin-channelsection-create',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ]);

        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-edit-add-ok'));
                return $this->redirect("/admin/channelsection/" . $node);

            } catch (FormModelException $e) {
                Message::error(Text::get('form-has-errors'));
            }
        }

        return $this->viewResponse('admin/channelsection/edit', [
            'current_node' => $node,
            'form' => $form->createView()
        ]);
    }

    public function editAction($node, $section_id, Request $request)
    {
        try {
            $channel = Node::get($node);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/channelsection');
        }

        $section = NodeSections::get($section_id);
        $processor = $this->getModelForm(AdminSectionForm::class, $section, (array)$section, [], $request);
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
                return $this->redirect("/admin/channelsection/" . $node);

            } catch (FormModelException $e) {
                Message::error(Text::get('form-has-errors'));
            }
        }

        return $this->viewResponse('admin/channelsection/edit', [
            'current_node' => $node,
            'program' => $section,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction($node, $section_id)
    {
        try {
            $section = NodeSections::get($section_id);
        } catch (ModelNotFoundException $exception) {
            Message::error($exception->getMessage());
        }

        try {
            $section->dbDelete();
            Message::info(Text::get('admin-remove-entry-ok'));
        } catch (PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/channelsection/' . $node);
    }

}
