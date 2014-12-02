<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model;

    class Preview extends \Goteo\Core\Controller {

        public function index ($model = null, $id = null, $view = null, $viewData = array() ) {

            return new View(
                'about/sample.html.php',
                array(
                    'name' => 'preview',
                    'title' => 'Preview',
                    'content' => 'preview'
                )
             );

        }

    }

}
