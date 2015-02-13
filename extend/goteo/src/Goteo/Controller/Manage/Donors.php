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

                $data = Model\User\Donor::getList($filters, true);

                // forzar descarga
                header('Content-type: text/csv; charset=utf-8');
                header("Content-disposition: attachment; filename=donantes_goteo{$filters['year']}_{$filters['status']}.csv");

                // cabecera
                echo 'NIF;NIF_REPRLEGAL;Nombre;Provincia;CLAVE;PORCENTAJE;VALOR;EN_ESPECIE;COMUNIDAD;PORCENTAJE_CA;NATURALEZA;REVOCACION;EJERCICIO;TIPOBIEN;BIEN
';

                foreach ($data as $id=>$row) {
                    echo implode(';', $row).';
';
                }

                return;
            }
/*
            if (!empty($filters['filtered'])) {
                $data = Model\User\Donor::getList($filters);
            } else {
                $data = array();
            }

,           'data'   => $data
*/
            return new View(
                'manage/index.html.php',
                array(
                    'folder' => 'donors',
                    'file'   => 'list',
                    'filters'=> $filters
                )
            );

        }

    }

}
