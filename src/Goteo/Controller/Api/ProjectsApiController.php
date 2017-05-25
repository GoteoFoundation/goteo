<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Model\Project;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Image;
use Goteo\Library\Text;


class ProjectsApiController extends AbstractApiController {

    /**
     * Simple listing of projects
     * TODO: according to permissions, filter this projects
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectsAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);
        $status = [
            Project::STATUS_IN_CAMPAIGN,
            Project::STATUS_FUNDED,
            Project::STATUS_FULFILLED,
            Project::STATUS_UNFUNDED,
        ];

        // General search
        if($request->query->has('q')) {
            $filters['global'] = $request->query->get('q');
        }
        if(!$this->is_admin) {
            $filters['multistatus'] = implode(",", $status);
        }

        if($request->query->has('status')) {
            $s = explode(",",preg_replace('/[^0-9,]/', '',$request->query->get('status')));
            if(!$this->is_admin) {
                $s = array_intersect($status, $s);
            }
            $filters['multistatus'] = implode(",", $s);
        }

        $limit = 25;
        $offset = $page * $limit;
        $total = Project::getList($filters, $node, 0, 0, true);
        $list = [];
        foreach(Project::getList($filters, $node, $offset, $limit) as $prj) {
            foreach(['id', 'name', 'owner', 'subtitle', 'status', 'node', 'published', 'success', 'passed', 'closed', 'video', 'image', 'lang', 'currency'] as $k)
                $ob[$k] = $prj->$k;
            foreach(['amount', 'mincost', 'maxcost'] as $k)
                $ob[$k] = (int)$prj->$k;
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
            ]);
    }

    /**
     * Simple projects info data
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectAction($id) {
        $prj = Project::getMini($id);
        if(!$this->is_admin && !in_array($prj->status, [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED])) {
            throw new ControllerAccessDeniedException();
        }
        $ob = [];
            foreach(['id', 'name', 'owner', 'subtitle', 'status', 'node', 'published', 'success', 'passed', 'closed', 'video', 'image', 'lang', 'currency'] as $k)
                $ob[$k] = $prj->$k;
            foreach(['amount', 'mincost', 'maxcost'] as $k)
                $ob[$k] = (int)$prj->$k;

        if($prj->image instanceof Image) {
            $ob['image'] = $prj->image->id;
        } else {
            $ob['image'] = $prj->image;
        }

        //add costs
        $ob['costs'] = [];
        foreach(Project\Cost::getAll($id) as $cost) {
            if(!is_array($ob['costs'][$cost->type])) $ob['costs'][$cost->type] = [];
            $ob['costs'][$cost->type][$cost->id] = ['cost' => $cost->cost, 'description' => $cost->description, 'amount' => (int)$cost->amount, 'required' => (bool)$cost->required];
        }

        return $this->jsonResponse($ob);
    }

    public function projectUploadImagesAction($id, Request $request) {
        $prj = Project::get($id);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = 'all-files-uploaded';
        $result = [];

        $section = $request->request->get('section');
        if(!array_key_exists($section, ProjectImage::sections())) {
            $section = '';
        }

        foreach($files as $file) {
            if(!$file instanceOf UploadedFile) continue;
            // Process image
            $msg = 'uploaded-ok';
            $success = false;
            $image = new Image($file);
            $errors = [];
            if ($image->save($errors)) {
                /**
                 * Guarda la relación NM en la tabla 'project_image'.
                 */
                if(!empty($image->id)) {
                    Project::query("REPLACE project_image (project, image, section) VALUES (:project, :image, :section)", array(':project' => $prj->id, ':image' => $image->id, ':section' => $section));
                }
                // recalculamos las galerias e imagen
                // getGallery en Project\Image  procesa todas las secciones
                // $galleries = Project\Image::getGalleries($this->id);
                // Project\Image::setImage($this->id, $galleries['']);
                $success = true;
            }
            else {
                $msg = implode(', ',$errors['image']);
                // print_r($errors);
                // Si hay errores al colgar una imagen, mostrar error correspondiente
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $image->name,
                'success' => $success,
                'msg' => $msg,
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'maxSize' => $file->getMaxFileSize(),
                'errorMsg' => $file->getErrorMessage()
            ];
            if(!$success) $global_msg = Text::get('project-upload-images-some-ko');
        }
        // ,$file->getPathName(),$file->getSize(),$file->getMimeType(),$file->getClientOriginalName()
        return $this->jsonResponse(['files' => $result, 'msg' => $global_msg]);
    }

    public function projectDeleteImagesAction($id, $image, Request $request) {
        $prj = Project::get($id);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $vars = array(':project' => $prj->id, ':image' => $image);
        Project::query("DELETE FROM project_image WHERE project = :project AND image = :image", $vars);
        $sql = "SELECT COUNT(*) FROM project_image WHERE project = :project AND image = :image";
        $success = (int) Project::query($sql, $vars)->fetchColumn() === 0;
        // $sql = "SELECT image FROM project WHERE id = :project";
        // if( Project::query($sql, ['project' => $prj->id])->fetchColumn() === $image) {
        //     Project::query("UPDATE project SET image = '' WHERE id = :project", ['project' => $prj->id]);
        // }
        return $this->jsonResponse(['image' => $image, 'result' => $success]);
    }

    public function projectDefaultImagesAction($id, $image, Request $request) {
        $prj = Project::get($id);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        $success = false;
        $msg = Text::get('dashboard-project-image-default-ko');
        if($prj->all_galleries) {
            $vars = array(':project' => $prj->id, ':image' => $image);
            foreach($prj->all_galleries as $key => $gal) {
                foreach($gal as $img) {
                    if($img->imageData->name === $image) {
                        // Set default
                        Project::query("UPDATE project SET image = :image WHERE id = :project", $vars);
                        $sql = "SELECT COUNT(*) FROM project WHERE id = :project AND image = :image";
                        $success = (int) Project::query($sql, $vars)->fetchColumn() === 1;
                        if($success) $msg = '';
                        break;
                    }
                }
                if($success) break;
            }
        }
        return $this->jsonResponse(['msg' => $msg, 'default' => $image, 'result' => $success]);
    }

    public function projectReorderImagesAction($id, Request $request) {
        $gallery = $request->request->get('gallery');

        $prj = Project::get($id);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        $success = false;
        $result = [];
        $msg = Text::get('dashboard-project-image-reorder-ko');
        if($gallery) {
            foreach($gallery as $section => $gal) {
                $index = 0;
                $result[$section] = $gal;
                $s = $section == '_' ? null : $section;
                foreach($gal as $img) {
                    $vars = array(':project' => $prj->id, ':image' => $img, ':section' => $s, ':order' => $index);
                    $sql = "UPDATE project_image SET `order` = :order, `section` = :section WHERE project = :project AND image = :image";
                    Project::query($sql, $vars);
                    $result[$section] = \sqldbg($sql, $vars);
                    $index++;
                }
            }
            // $success = true;
            // $msg = '';
        }
        if($prj->all_galleries) {
            $vars = array(':project' => $prj->id, ':image' => $image);
            foreach($prj->all_galleries as $key => $gal) {
                foreach($gal as $img) {
                    if($img->imageData->name === $image) {
                        // break;
                    }
                }
                if($success) break;
            }
        }
        return $this->jsonResponse(['msg' => $msg, 'gallery' => $result, 'result' => $success]);
    }
}
