<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Template,
		Goteo\Library\Mail;

    class Sended {

        public static function process ($action = 'list', $id = null) {

            $filters = array();
            $fields = array('user', 'template');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $templates = Template::getAllMini();

            $sended = Mail::getSended($filters);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'sended',
                    'file' => 'list',
                    'filters' => $filters,
                    'templates' => $templates,
                    'sended' => $sended
                )
            );
            
        }

    }

}
