<?php

namespace Goteo\Controller\Admin;

class WordcountSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $wordcount = array();

        return array(
                'folder' => 'base',
                'file' => 'wordcount',
                'wordcount' => $wordcount
        );

    }

}

