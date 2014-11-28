<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Wordcount {

        public static function process ($action = 'list', $id = null) {

            $wordcount = array();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'wordcount',
                    'wordcount' => $wordcount
                )
            );

        }

    }

}
