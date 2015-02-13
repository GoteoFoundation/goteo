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
        static public $availables = array('donors', 'investors');

        public function index ($cual = null, $id = null) {
            if (!in_array($cual, self::$availables)) {
                throw new Redirection('/admin');
            }

            $data = array();
            
            // el id es necesario cuando el sacaexcel es por proyecto

            // obtenemos las cabeceras y los datos
            switch ($cual) {
                case 'donors':

                    $filters = array(
                        'year' => $_GET['year'],
                        'status' => $_GET['status'],
                        'user' => $_GET['user']
                    );
                    
                    if (empty($filters['year']))
                        $filters['year'] = Model\User\Donor::currYear();

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
                    break;
                    
                // cofinanciadores de un proyecto (para impulsor, tiene en cuenta email oculto)
                case 'investors':

//                                IF (invest.resign, 'Donativo', '') as resign,
                    $sql = "SELECT  
                                invest.id as id,
                                invest.user as user,
                                IF(invest_address.name != '', invest_address.name, user.name) as name,
                                user_prefer.email as noemail,
                                IF(user_prefer.email = 1, '', user.email) as email,
                                invest.amount as amount,
                                IF (invest.issue = 1, 'Incidencia', '') as issue,
                                IF (invest.anonymous = 1, 'Anonimo', '') as anonymous,
                                IF(invest_address.address,
                                    concat(invest_address.address, ', ', invest_address.zipcode, ', ', invest_address.location, ', ', invest_address.country),
                                    concat(user_personal.address, ', ', user_personal.zipcode, ', ', user_personal.location, ', ', user_personal.country)
                                    ) as address,
                                date_format(invest.invested, '%d/%m/%Y') as date
                            FROM  invest
                            INNER JOIN user ON user.id = invest.user
                            LEFT JOIN invest_address ON invest_address.invest = invest.id
                            LEFT JOIN user_personal ON user_personal.user = invest.user
                            LEFT JOIN user_prefer ON user_prefer.user = invest.user
                            WHERE   invest.project = ?
                            AND invest.status IN ('1', '3')
                            ORDER BY invest.amount ASC
                            ";

                    $query = \Goteo\Core\Model::query($sql, array($id));
                    foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {

                        // recompensa
                        $subQuery = \Goteo\Core\Model::query("SELECT reward.reward
                                                FROM reward, invest_reward
                                                WHERE reward.id = invest_reward.reward
                                                AND invest_reward.invest = ?
                            ", array($item->id));
                        $item->rewards = $subQuery->fetchColumn();

                        $data[] = $item;
                    }
                    $columns = array(
                        'id' => 'Aporte',
                        'user' => 'Usuario',
                        'name' => 'Nombre',
                        'email' => 'Email',
                        'amount' => 'Importe',
                        'issue' => 'Incidencia',
//                        'resign' => 'Renuncia',
                        'anonymous' => 'Anonimo',
                        'rewards' => 'Recompensas',
                        'address' => 'Direccion',
                        'date' => 'Fecha'
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
            <?php foreach ($columns as $col=>$label) echo "<td style=\"font-weight: bold;\">{$label}</td>"; ?>
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