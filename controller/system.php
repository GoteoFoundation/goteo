<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Model,
		Goteo\Library\Text,
		Goteo\Library\Feed;

	class System extends \Goteo\Core\Controller {


        public function index ($tool = 'index') {

            switch ($tool) {
                case 'lasts':
                    // get lasts 10 users registered
                    $sql = "SELECT id, name, email, confirmed, created
                        FROM user
                        ORDER BY created DESC
                        LIMIT 10
                        ";
                    $query = Model::query($sql);
                    $data = $query->fetchAll(\PDO::FETCH_OBJ);

                    break;

                default:
                    // get lasts 10 users registered
                    $sql = "SELECT id, name, email, confirmed, created
                        FROM user
                        ORDER BY created DESC
                        LIMIT 10
                        ";
                    $query = Model::query($sql);
                    $data = $query->fetchAll(\PDO::FETCH_OBJ);
                    
                    break;
            }

            return new View('view/system/index.html.php', array('data'=>$data));
        }


	}

}
