<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Invests {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // edición
            if ($action == 'edit') {

//                $contract = Model\Contract::get($id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'contracts',
                        'file' => 'edit',
                        'contract' => $contract
                    )
                );
            }

            // previsualización
            if ($action == 'preview') {

//                $contract = Model\Contract::get($id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'contracts',
                        'file' => 'edit',
                        'contract' => $contract
                    )
                );
            }

            // listado de contratos
            if ($filters['filtered'] == 'yes') {
//                $list = Model\Contract::getAll($filters, $_SESSION['admin_node']);
                $list = array(1, 2, 3);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'contracts',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
