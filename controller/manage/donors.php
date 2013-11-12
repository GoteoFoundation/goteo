<?php

namespace Goteo\Controller\Manage {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Reporting,
        Goteo\Model;

    class Donors {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            if ($action == 'excel') {
                $filters = array(
                    'year' => $_GET['year'],
                    'status' => $_GET['status']
                );
                
                $data = Model\User\Donor::getList($filters);
                $columns = array(
                    'id' => 'Usuario',
                    'email' => 'Email',
                    'name' => 'Nombre',
                    'nif' => 'NIF',
                    'address' => 'Direccion',
                    'zipcode' => 'Zipcode',
                    'location' => 'Localidad',
                    'country' => 'Pais',
                    'amount' => 'Cantidad',
                    'numproj' => 'Projs.',
                    'year' => 'A&ntilde;o',
                    'pending' => 'Pendiente',
                    'edited' => 'Editados',
                    'confirmed' => 'Confirmados',
                    'pdf' => 'Certificado'
                );

                ?>
                <html>
                    <table border="1">
                        <tr>
                            <?php foreach ($columns as $col=>$label) echo 'td style="font-weight: bold;">'.$label.'</td>'; ?>
                        </tr>
                        <?php foreach ($data as $id=>$row) : ?>
                        <tr>
                            <?php foreach ($columns as $col=>$label)  
                                echo (is_object($row)) ? '<td>'.$row->$col.'</td>' : '<td>'.$row[$col].'</td>'; 
                            ?>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </html>
                <?php

                return;
            }


            if ($action == 'resetpdf' && !empty($id) ) {
                Model\User\Donor::resetPdf($id);
            }

            if (empty($filters['year']))
                $filters['year'] = 2013;

            $data = Model\User\Donor::getList($filters);

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
