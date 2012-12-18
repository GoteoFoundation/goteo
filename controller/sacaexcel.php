<?php
/*
 * Clase que saca una tabla plana para copypaste a excel
 */
namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Model;

    class Sacaexcel extends \Goteo\Core\Controller {

        // Array de sacaexcels programados
        static public $availables = array('donors');

        public function index ($cual = null, $id = null) {
            if (!in_array($cual, self::$availables)) {
                throw new Redirection('/admin');
            }

            // el id es necesario cuando el sacaexcel es por proyecto

            // obtenemos las cabeceras y los datos
            switch ($cual) {
                case 'donors':
                    $data = Model\User\Donor::getList(array('year'=>'2012'));
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
                        'confirmed' => 'Confirmados'
                    );
                    break;
                default:
                    $columns = array(
                        'col1' => 'Columna 1',
                        'col2' => 'Columna 2',
                        'col3' => 'Columna 3'
                    );

                    $data = array(
                        'A' => array(
                            'col1' => 'Valor A1',
                            'col2' => 'Valor A2',
                            'col3' => 'Valor A3',
                        ),
                        'B' => array(
                            'col1' => 'Valor B1',
                            'col2' => 'Valor B2',
                            'col3' => 'Valor B3',
                        ),
                        'C' => array(
                            'col1' => 'Valor C1',
                            'col2' => 'Valor C2',
                            'col3' => 'Valor C3',
                        )
                    );
            }


// pintamos //
?>
<html>
    <table border="1">
        <tr>
            <?php foreach ($columns as $col=>$label) echo "<td>{$label}</td>"; ?>
        </tr>
        <?php foreach ($data as $id=>$row) : ?>
        <tr>
            <?php foreach ($columns as $col=>$label) { if (is_object($row)) echo "<td>{$row->$col}</td>"; else  echo "<td>{$row[$col]}</td>"; }?>
        </tr>
        <?php endforeach; ?>
    </table>
</html>
<?php
        }

    }

}