<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Core\Model,
        Goteo\Core\View;

	class Mail extends \Goteo\Core\Controller {

	    /**
	     * solo si recibe un token válido
	     */
		public function index ($token) {

            if (!empty($token) && !empty($_GET['email'])) {
                $token = base64_decode($token);
                $parts = explode('¬', $token);
                if(count($parts) > 2 && ($_GET['email'] == $parts[1] || $parts[1] == 'any' ) && !empty($parts[2])) {
                    // cogemos el contenido de la bbdd y lo pintamos aqui tal cual
                    if ($query = Model::query('SELECT html FROM mail WHERE email = ? AND id = ?', array($parts[1], $parts[2]))) {
                        $content = $query->fetchColumn();
                        $baja = SEC_URL . '/user/leave/?email=' . $parts[1];
                        if ($parts[1] == 'any') {
                            return new View ('view/email/newsletter.html.php', array('content'=>$content, 'baja' => ''));
                        } else {
                            if (NODE_ID != \GOTEO_NODE && \file_exists('nodesys/'.NODE_ID.'/view/email/default.html.php')) {
                                return new View ('nodesys/'.NODE_ID.'/view/email/default.html.php', array('content'=>$content, 'baja' => $baja));
                            } else {
                                return new View ('view/email/goteo.html.php', array('content'=>$content, 'baja' => $baja));
                            }
                        }
                    }
                }
            }

            throw new Redirection('/');
		}

    }

}