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
            if ($action == 'manage') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/manage/index.html.php',
                    array(
                        'folder' => 'contracts',
                        'file' => 'manage',
                        'contract' => $contract
                    )
                );
            }

            // previsualizar y crear pdf
            if ($action == 'preview') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/manage/index.html.php',
                    array(
                        'folder'   => 'contracts',
                        'file'     => 'preview',
                        'contract' => $contract
                    )
                );
            }

            
            // proyectos en campaña o financiados
            $list  = Model\Project::active();

            // estados de proyecto
            $status = Model\Project::status();
            
            // contratos
            $contracts = Model\Contract::getAll();
            
             $viewData = array(
                    'folder'   => 'contracts',
                    'file'     => 'list',
                    'list'     => $list,
                    'status'   => $status,
                    'contracts'=> $contracts,
                    'filters'  => $filters
                );

            return new View(
                'view/manage/index.html.php',
                $viewData
            );

        }

    }

}
