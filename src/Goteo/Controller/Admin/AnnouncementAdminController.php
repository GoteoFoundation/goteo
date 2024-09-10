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

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Announcement;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\AnnouncementForm;
use Goteo\Repository\AnnouncementRepository;

class AnnouncementAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-file-text-o"></i>';

    protected static AnnouncementRepository $announcementRepository;

    public function __construct()
    {
        $this->announcementRepository = new AnnouncementRepository();
    }

    public static function getGroup(): string
    {
        return 'communications';
    }

    public static function getRoutes(): array
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/edit/{id}',
                [
                    '_controller' => __CLASS__ . "::editAction"
                ]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/delete/{id}',
                ['_controller' => __CLASS__ . "::deleteAction"]
            )
        ];
    }

    public function listAction(Request $request)
    {

        $count = $this->announcementRepository->count();
        $announcements = $this->announcementRepository->getList(0, $count);


        return $this->viewResponse('admin/announcements/list', [
            'list' => $announcements,
            'total' => $count,
            'limit' => $count,
        ]);
    }

    public function editAction(Request $request, int $id = null)
    {

        if ($id) {
            $announcement = $this->announcementRepository->getById($id);
        } else {
            $announcement = new Announcement();
        }

        $defaults = [];

        $processor = $this->getModelForm(AnnouncementForm::class, $announcement, $defaults, [], $request);
        $processor->createForm();

        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/admin/announcement");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/announcements/edit', [
            'announcement' => $announcement,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction(Request $request, int $id)
    {

        try {
            $announcement = $this->announcementRepository->getById($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/announcement');
        }

        try {
            $this->announcementRepository->delete($announcement);
        } catch (ModelException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/announcement');
    }
}
