<?php

namespace Goteo\Controller\Manage {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Reporting,
        Goteo\Model;

    class Donors {

        public static function process ($action = 'list', $id = null, $subaction = null, $filters = array()) {

            if ($action == 'excel') {
                $filters = array(
                    'year' => $_GET['year'],
                    'status' => $_GET['status']
                );
                
                $data = Model\User\Donor::getList($filters, true);

                // forzar descarga
                header('Content-type: text/csv');
                header("Content-disposition: attachment; filename=donantes_goteo".$filters['year'].".csv");

                foreach ($data as $id=>$row) {
                    echo implode(';', $row).';
';
                }

                return;
            }


            if ($action == 'resetpdf' && !empty($id) ) {
                Model\User\Donor::resetPdf($id);
            }

            if (empty($filters['year']))
                $filters['year'] = Model\User\Donor::$currYear;

            if (!empty($filters['filtered'])) {
                $data = Model\User\Donor::getList($filters);
            } else {
                $data = array();
            }

            return new View(
                'view/manage/index.html.php',
                array(
                    'folder' => 'donors',
                    'file'   => 'list',
                    'filters'=> $filters,
                    'data'   => $data
                )
            );

        }

    }

}
