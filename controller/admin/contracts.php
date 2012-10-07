<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Contracts {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // edición
            if ($action == 'edit') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'contracts',
                        'file' => 'edit',
                        'contract' => $contract
                    )
                );
            }

            // previsualizar y crear pdf
            if ($action == 'preview') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder'   => 'contracts',
                        'file'     => 'preview',
                        'contract' => $contract
                    )
                );
            }

            // listado de contratos
            if ($filters['filtered'] == 'yes') {
                $list = Model\Contract::getAll($filters);
            } else {
                $list = array();
            }

            $projects = Model\Contract::getProjects();
            
             $viewData = array(
                    'folder'   => 'contracts',
                    'file'     => 'list',
                    'list'     => $list,
                    'projects' => $projects,
                    'filters'  => $filters
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
