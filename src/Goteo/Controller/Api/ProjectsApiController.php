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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterProjectPostEvent;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Reward;
use Goteo\Model\Image;
use Goteo\Library\Text;
use Goteo\Console\UsersSend;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;

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
        $filters = ['order' => 'updated DESC, created DESC'];
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
            $filters['basic'] = $request->query->get('q');
        }
        if(!$this->is_admin) {
            $filters['status'] = $status;
        }

        if($request->query->has('status')) {
            $s = explode(",",preg_replace('/[^0-9,]/', '',$request->query->get('status')));
            if(!$this->is_admin) {
                $s = array_intersect($status, $s);
            }
            $filters['status'] = $s;
        }

        $limit = 25;
        $offset = $page * $limit;
        $total = Project::getList($filters, $node, 0, 0, true);
        $list = [];
        foreach(Project::getList($filters, $node, $offset, $limit) as $prj) {
            foreach(['id', 'name', 'owner', 'subtitle', 'node', 'created', 'updated', 'published', 'project_location', 'success', 'passed', 'closed', 'video', 'lang', 'currency'] as $k)
                $ob[$k] = $prj->$k;
            foreach(['status', 'amount', 'mincost', 'maxcost'] as $k)
                $ob[$k] = (int)$prj->$k;
            $ob['image'] = $prj->image ? $prj->image->getLink(64,64,true) : null;
            $ob['status_desc'] = $prj->getTextStatus();
            $ob['url'] = '/project/' . $prj->id;
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
            ]);
    }

    protected function getSafeProject($prj) {
        if(!$prj instanceOf Project) $prj = Project::get($prj);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        $ob = [];
        foreach(['id', 'name', 'owner', 'subtitle', 'description','status', 'node', 'published','project_location', 'success', 'passed', 'closed', 'video', 'image', 'lang', 'currency'] as $k)
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
        foreach(Project\Cost::getAll($prj->id) as $cost) {
            if(!is_array($ob['costs'][$cost->type])) $ob['costs'][$cost->type] = [];
            $ob['costs'][$cost->type][$cost->id] = ['cost' => $cost->cost, 'description' => $cost->description, 'amount' => (int)$cost->amount, 'required' => (bool)$cost->required];
        }
        return $ob;
    }

    /**
     * Simple projects info data
     */
    public function projectAction($id) {
        // $prj = Project::getMini($id);
        // // if(!$this->is_admin && !in_array($prj->status, [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED])) {
        // if(!$prj->userCanView($this->user)) {
        //     throw new ControllerAccessDeniedException();
        // }
        $properties = $this->getSafeProject($id);

        return $this->jsonResponse($properties);
    }


    /**
     * Individual project property checker/updater
     * To update a property, use the PUT method
     */
    public function projectPropertyAction($id, $prop, Request $request) {
        $prj = Project::get($id);
        $properties = $this->getSafeProject($prj);
        $write_fields = ['name', 'subtitle', 'description'];
        if(!isset($properties[$prop])) {
            throw new ModelNotFoundException("Property [$prop] not found");
        }

        $result = ['value' => $properties[$prop], 'error' => false];

        if($request->isMethod('put')) {
            if(!$prj->userCanEdit($this->user)) {
                throw new ControllerAccessDeniedException();
            }
            if(!$prj->inEdition()) {
                throw new ControllerAccessDeniedException(Text::get('dashboard-project-not-alive-yet'));
            }

            if(!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }
            $prj->{$prop} = $request->request->get('value');

            // do the SQL update
            $prj->dbUpdate([$prop]);
            $result['value'] = $prj->{$prop};
        }
        return $this->jsonResponse($result);

    }

    /**
     * Individual project updates property checker/updater
     * To update a property, use the PUT method
     */
    public function projectUpdatesPropertyAction($pid, $uid, $prop, Request $request) {
        $prj = Project::get($pid);
        $post = BlogPost::get($uid);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        if(!$prj->isApproved()) {
            throw new ControllerAccessDeniedException(Text::get('dashboard-project-blog-wrongstatus'));
        }

        if(!$post) throw new ModelNotFoundException();
        if($post->owner_id !== $prj->id) throw new ModelNotFoundException('Non matching update');

        $read_fields = ['id', 'title', 'text', 'media', 'date', 'author', 'allow', 'publish', 'image', 'gallery', 'owner_type', 'owner_id', 'owner_name', 'user_name'];
        $write_fields = ['title', 'text', 'date', 'allow', 'publish'];
        $properties = [];
        foreach($read_fields as $f) {
            if(isset($post->{$f})) {
                $val = $post->{$f};
                if($val instanceOf Image) {
                    $val = $val->getName();
                }
                if(is_array($val)) {
                    foreach($val as $i => $ssub) {
                        if($sub instanceOf Image) {
                            $val[$i] = $sub->getName();
                        }
                    }
                }
                if(in_array($f, ['allow', 'publish'])) {
                    $val = (bool) $val;
                }
                $properties[$f] = $val;
            }
        }
        if(!array_key_exists($prop, $properties)) {
            throw new ModelNotFoundException("Property [$prop] not found");
        }
        $result = ['value' => $properties[$prop], 'error' => false];
        if($request->isMethod('put') && $request->request->has('value')) {
            if(!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }
            $post->{$prop} = $request->request->get('value');

            if(in_array($prop, ['allow', 'publish'])) {
                if($post->{$prop} == 'false') $post->{$prop} = false;
                if($post->{$prop} == 'true') $post->{$prop} = true;
                $post->{$prop} = (bool) $post->{$prop};
            }

            // do the SQL update
            $post->dbUpdate([$prop]);
            $result['value'] = $post->{$prop};
            $this->dispatch(AppEvents::PROJECT_POST, new FilterProjectPostEvent($post));
            // if($errors = Message::getErrors()) throw new ControllerException(implode("\n",$errors));
            if($errors = Message::getErrors()) {
                $result['error'] = true;
                $result['message'] = implode("\n", $errors);
            }
            if($messages = Message::getMessages()) {
                $result['message'] = implode("\n", $messages);
            }

        }
        return $this->jsonResponse($result);
    }

    protected function validateProject($pid) {
        $prj = Project::get($pid);

        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $this->admin = $prj->userCanModerate($this->user);

        if($this->admin || $prj->inEdition() || $prj->isAlive()) {
            return $prj;
        }

        throw new ControllerAccessDeniedException(Text::get('dashboard-project-not-alive-yet'));
    }

    /**
     * AJAX upload image (Generic uploader with optional project gallery updater)
     */
    public function projectUploadImagesAction($id, Request $request) {
        $prj = $this->validateProject($id);

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $add_to_gallery = $request->request->get('add_to_gallery');

        $section = $request->request->get('section');
        if(!array_key_exists($section, ProjectImage::sections())) {
            $section = '';
        }

        $cover = $prj->image->id ? $prj->image->id : null;
        $all_success = true;
        foreach($files as $file) {
            if(!$file instanceOf UploadedFile) continue;
            // Process image
            $msg = Text::get('uploaded-ok');
            $success = false;
            if($err = Image::getUploadErrorText($file->getError())) {
                $success = false;
                $msg = $err;
            } else {
                $image = new Image($file);
                $errors = [];
                if ($image->save($errors)) {

                    if($add_to_gallery === 'project_image') {
                        /**
                         * Guarda la relación NM en la tabla 'project_image'.
                         */
                        if($image->id) {
                            Project::query("REPLACE project_image (project, image, section) VALUES (:project, :image, :section)", array(':project' => $prj->id, ':image' => $image->id, ':section' => $section));
                            if(!$prj->image->id) {
                                // Set default image
                                Project\Image::setImage($prj->id, $image);
                                $cover = $image->id;
                            }
                        }
                    }

                    $success = true;
                }
                else {
                    $msg = implode(', ',$errors['image']);
                    // print_r($errors);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $image->name,
                'success' => $success,
                'msg' => $msg,
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'maxSize' => $file->getMaxFileSize(),
                'errorMsg' => $file->getError() ? $file->getErrorMessage() : ''
            ];
            if(!$success) {
                $global_msg = Text::get('project-upload-images-some-ko');
                $all_success = false;
            }
        }

        return $this->jsonResponse(['files' => $result, 'cover' => $cover,  'msg' => $global_msg, 'success' => $all_success]);
    }

    public function projectDeleteImagesAction($id, $image, Request $request) {
        $prj = $this->validateProject($id);

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
        $prj = $this->validateProject($id);

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

        $prj = $this->validateProject($id);

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
                    // $result[$section] = \sqldbg($sql, $vars);
                    $index++;
                }
            }
            $success = true;
            $msg = '';
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

    public function projectMaterialsAction($id, Request $request) {
        $prj = Project::get($id);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        // Handle PUT requests: new element
        if($request->isMethod('put')) {
            if(!$prj->userCanEdit($this->user)) {
                throw new ControllerAccessDeniedException();
            }
            if(!$prj->isFunded()) {
                throw new ControllerAccessDeniedException(Text::get('dashboard-project-not-alive-yet'));
            }
            // save new material
            $url = $request->request->get('url');
            $reward_id = $request->request->get('reward');

            $reward = Reward::get($reward_id);
            if(!$reward)
                throw new ModelNotFoundException("Not found reward $reward_id");

            $reward->url = $url;

            $reward->updateURL();

            $rol = Text::get('user-promoter');

            // Send email to all default consultants
            $consultants = $prj->getConsultants();
            if($always_consultants = Config::get('mail.consultants')) {
                $consultants += $always_consultants;
            }

            $prj->whodidit = $this->user->id;
            $prj->whorole = $rol;

            // For compatibility with old version of sendUsers
            $_POST['reward'] = $reward_id;
            $_POST['value'] = $url;

            UsersSend::setURL(Config::getUrl($prj->lang));
            UsersSend::toConsultants('rewardfulfilled', $prj);

            $prj->social_rewards[$reward->id] = $reward;
        }

        // Handles POST requests (new element)
        if($request->isMethod('post')) {

            $material = $request->request->get('material');
            $description = $request->request->get('description');
            $icon = $request->request->get('icon');
            $license = $request->request->get('license');
            $url = $request->request->get('url');

            $reward = new Reward();

            $reward->project = $prj->id;
            $reward->reward = $material;
            $reward->description = $description;
            $reward->icon = $icon;
            $reward->license = $license;
            $reward->url = $url;
            $reward->bonus = 1;
            $reward->type = "social";

            if(!$reward->save($errors)) {
                throw new ModelException(implode(',', $errors));
            }
            $prj->social_rewards[$reward->id] = $reward;
        }

        $materials = $prj->social_rewards;
        return $this->jsonResponse($materials);
    }

    // Fulfilled status
    public function projectInvestsFulfilledAction($pid, $iid, Request $request) {
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        if(!$prj->isAlive()) {
            throw new ControllerAccessDeniedException(Text::get('dashboard-project-not-alive-yet'));
        }

        $invest = Invest::get($iid);
        if($invest->getProject()->id !== $prj->id) {
            throw new ControllerAccessDeniedException("Invest [$iid] not in project [$pid]");
        }

        if($request->isMethod('put')) {
            $fulfilled = (bool)$request->request->get('value');
            // TODO: check who can set to false this property
            if($fulfilled) {
                $invest->fulfilled = Invest::setFulfilled($invest);
                if($invest->fulfilled != $fulfilled) {
                    throw new ModelException("Error setting invest as fulfilled");
                }
            }
        }

        return $this->jsonResponse(['value' => (bool) $invest->fulfilled]);
    }

    // CSV Extraction
    public function projectInvestsCSVAction($pid, Request $request) {
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        if(!$prj->isApproved()) {
            throw new ControllerAccessDeniedException(Text::get('dashboard-project-not-alive-yet'));
        }

        $limit = 10;
        $order = 'invested DESC';
        $filter = ['projects' => $prj->id, 'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_RELOCATED, Invest::STATUS_TO_POOL]];
        $total = Invest::getList($filter, null, 0, 0, true);

        $response = new StreamedResponse(function () use ($filter, $total, $limit, $order) {
            $buffer = fopen('php://output', 'w');
            $data = [Text::get('regular-input'),
                     Text::get('admin-user'),
                     Text::get('regular-name'),
                     Text::get('regular-email'),
                     Text::get('regular-amount'),
                     Text::get('regular-status'),
                     Text::get('dashboard-rewards-issue'),
                     Text::get('dashboard-rewards-resigns'),
                     Text::get('regular-anonymous'),
                     Text::get('overview-field-reward'),
                     Text::get('dashboard-rewards-fulfilled_status'),
                     Text::get('admin-address'),
                     Text::get('regular-date')];
            fputcsv($buffer, $data);
            flush();
            fclose($buffer);
            $offset = 0;
            while ($results = Invest::getList($filter, null, $offset, $limit, false, $order)) {
                // Open and close the buffer to save memory
                $buffer = fopen('php://output', 'w');
                foreach ($results as $inv) {
                    $resign = $inv->resign;
                    $id = $inv->getUser()->id;
                    $name = $inv->getUser()->name;
                    $email = $inv->getUser()->email;
                    $a = $inv->getAddress();
                    $address = $a->address . ', ' . $a->location . ', ' . $a->zipcode .' ' . $a->country;
                    $reward = $inv->getRewards() ? $inv->getRewards()[0]->getTitle() : '';
                    if($inv->resign) {
                        $reward = $address = '';
                        if($inv->anonymous) {
                            $id = $name = $email = '';
                        }
                    }
                    if($inv->campaign) {
                        $email = $address = $reward = '';
                        $name .= ' (' . Text::get('regular-matchfunding') . ')';
                        $resign = true;
                    }
                    $data = [$inv->id,
                             $id,
                             $name,
                             $email,
                             number_format($inv->amount, 2, ',', ''),
                             $inv->getStatusText(true),
                             Text::get('regular-' . ($inv->issue ? 'yes' : 'no')),
                             Text::get('regular-' . ($resign ? 'yes' : 'no')),
                             Text::get('regular-' . ($inv->anonymous ? 'yes' : 'no')),
                             $reward,
                             Text::get('regular-' . ($inv->fulfilled ? 'yes' : 'no')),
                             $address,
                             date_formater($inv->invested) ];
                    fputcsv($buffer, $data);
                }
                $offset += $limit;
                flush();
                fclose($buffer);
            }
        });

        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $prj->id . '_rewards_' . date('Y-m-d') . '.csv'
        );

        $response->headers->set('Content-Disposition', $d);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}

