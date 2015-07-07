<?php

namespace Goteo\Controller {

    use Goteo\Application\Session;
    use Goteo\Application\Exception\ControllerException;
    use Goteo\Application\Exception\ControllerAccessDeniedException;
    use Goteo\Library\FileHandler\File,
        Goteo\Model;

    class DocumentController extends \Goteo\Core\Controller {

        public function indexAction($id, $filename = '') {
            $doc = Model\Contract\Document::get($id);
            $project = Model\Project::get($doc->contract);
            if(!$project->userCanManage(Session::getUser())) {
                throw new ControllerAccessDeniedException("Error: You don't have permissions to access to this document!");
            }

            // pero ojo porque al ser el archivo privado quizas habra que coger los contenidos
            // mime type en el header
            $fp = File::factory(array('bucket' => AWS_S3_BUCKET_DOCUMENT));
            $fp->setPath($doc->filedir);

            $content = $fp->get_contents($doc->name);

            if(!$content) {
                throw new ControllerException('Error: Empty document!');
            }

            return $this->rawResponse($content, $doc->type);

        }

        public function certAction($user, $year) {
            $pdf = Model\User\Donor::getPdf($user, $year);
            $user_ob = Model\User::get($user);
            if(!$user_ob) {
                throw new ControllerException('User not found! ['.$user.']');
            }

            $me = Session::getUser();
            if(!$me || !$me->hasRoleInNode($user_ob->node, ['manager', 'superadmin', 'root'])) {
                throw new ControllerAccessDeniedException("Error: You don't have permissions to access to this document!");
            }

            if (empty($pdf)) {
                throw new ControllerException('No se ha generado el certificado de '.$user.' para '.$year);
            }

            $fp = File::factory(array('bucket' => AWS_S3_BUCKET_DOCUMENT));
            $fp->setPath('certs/');

            // archivo
            $content = $fp->get_contents($pdf);

            if(!$content) {
                throw new ControllerException('Error: Empty document!');
            }

            return $this->rawResponse($content, $doc->type);
        }

    }

}
