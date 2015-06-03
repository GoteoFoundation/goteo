<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Library\Template,
        Goteo\Library\Mail,
        Goteo\Library\Newsletter,
        Goteo\Application\Lang,
        Goteo\Model;

    class Mailing {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            // Valores de filtro
            $interests = Model\User\Interest::getAll();
            $status = Model\Project::status();
            $methods = Model\Invest::methods();
            $types = array(
                'investor' => 'Cofinanciadores',
                'owner' => 'Autores',
                'user' => 'Usuarios'
            );
            $roles = array(
                'admin' => 'Administrador',
                'checker' => 'Revisor',
                'translator' => 'Traductor'
            );
            $langs = Lang::listAll('object', false);

            // una variable de sesion para mantener los datos de todo esto
            if (!isset($_SESSION['mailing'])) {
                $_SESSION['mailing'] = array();
            }

            switch ($action) {
                case 'edit':

                    $_SESSION['mailing']['receivers'] = array();

                    $values = array();
                    $sqlFields  = '';
                    $sqlInner  = '';
                    $sqlFilter = '';


                    // cargamos los destiantarios
                    //----------------------------
                    // por tipo de usuario
                    switch ($filters['type']) {
                        case 'investor':
                            $sqlInner .= "INNER JOIN invest
                                    ON invest.user = user.id
                                    AND (invest.status = 0 OR invest.status = 1 OR invest.status = 3 OR invest.status = 4)
                                INNER JOIN project
                                    ON project.id = invest.project
                                    ";
                            $sqlFields .= ", project.name as project";
                            $sqlFields .= ", project.id as projectId";
                            break;
                        case 'owner':
                            $sqlInner .= "INNER JOIN project
                                    ON project.owner = user.id
                                    ";
                            $sqlFields .= ", project.name as project";
                            $sqlFields .= ", project.id as projectId";
                            break;
                        default :
                            break;
                    }
                    $_SESSION['mailing']['filters_txt'] = 'los <strong>' . $types[$filters['type']] . '</strong> ';

                    if (!empty($filters['project']) && !empty($sqlInner)) {
                        $sqlFilter .= " AND project.name LIKE (:project) ";
                        $values[':project'] = '%'.$filters['project'].'%';
                        $_SESSION['mailing']['filters_txt'] .= 'de proyectos que su nombre contenga <strong>\'' . $filters['project'] . '\'</strong> ';
                    } elseif (empty($filters['project']) && !empty($sqlInner)) {
                        $_SESSION['mailing']['filters_txt'] .= 'de cualquier proyecto ';
                    }

                    if (isset($filters['status']) && $filters['status'] > -1 && !empty($sqlInner)) {
                        $sqlFilter .= "AND project.status = :status ";
                        $values[':status'] = $filters['status'];
                        $_SESSION['mailing']['filters_txt'] .= 'en estado <strong>' . $status[$filters['status']] . '</strong> ';
                    } elseif ($filters['status'] < 0 && !empty($sqlInner)) {
                        $_SESSION['mailing']['filters_txt'] .= 'en cualquier estado ';
                    }

                    if ($filters['type'] == 'investor') {
                        if (!empty($filters['method']) && !empty($sqlInner)) {
                            $sqlFilter .= "AND invest.method = :method ";
                            $values[':method'] = $filters['method'];
                            $_SESSION['mailing']['filters_txt'] .= 'mediante <strong>' . $methods[$filters['method']] . '</strong> ';
                        } elseif (empty($filters['method']) && !empty($sqlInner)) {
                            $_SESSION['mailing']['filters_txt'] .= 'mediante cualquier metodo ';
                        }
                    }

                    if (!empty($filters['interest'])) {
                        $sqlInner .= "INNER JOIN user_interest
                                ON user_interest.user = user.id
                                AND user_interest.interest = :interest
                                ";
                        $values[':interest'] = $filters['interest'];
                        if ($filters['interest'] == 15) {
                            $_SESSION['mailing']['filters_txt'] .= 'del grupo de testeo ';
                        } else {
                            $_SESSION['mailing']['filters_txt'] .= 'interesados en fin <strong>' . $interests[$filters['interest']] . '</strong> ';
                        }
                    }

                    if (!empty($filters['role'])) {
                        $sqlInner .= "INNER JOIN user_role
                                ON user_role.user_id = user.id
                                AND user_role.role_id = :role
                                ";
                        $values[':role'] = $filters['role'];
                        $_SESSION['mailing']['filters_txt'] .= 'que sean <strong>' . $roles[$filters['role']] . '</strong> ';
                    }

                    if (!empty($filters['name'])) {
                        $sqlFilter .= " AND ( user.name LIKE (:name) OR user.email LIKE (:name) ) ";
                        $values[':name'] = '%'.$filters['name'].'%';
                        $_SESSION['mailing']['filters_txt'] .= 'que su nombre o email contenga <strong>\'' . $filters['name'] . '\'</strong> ';
                    }

                    if ($node != \GOTEO_NODE) {
                        $sqlFilter .= " AND user.node = :node";
                        $values[':node'] = $node;
                        if (!empty($sqlInner)) {
                            $sqlFilter .= " AND project.node = :node";
                        }
                    }

                    if (!empty($filters['comlang'])) {
                        $sqlInner .= "INNER JOIN user_prefer
                                ON user_prefer.user = user.id
                                AND user_prefer.comlang = :comlang
                                ";
                        $values[':comlang'] = $filters['comlang'];
                        $_SESSION['mailing']['filters_txt'] .= 'con idioma preferencia <strong>' . $langs[$filters['comlang']]->short . '</strong> ';
                    }

                    $sql = "SELECT
                                user.id as id,
                                user.id as user,
                                user.name as name,
                                user.email as email
                                $sqlFields
                            FROM user
                            $sqlInner
                            WHERE user.active = 1
                            $sqlFilter
                            GROUP BY user.id
                            ORDER BY user.name ASC
                            ";

                    // die( \sqldbg($sql, $values) );

                    if ($query = Model\User::query($sql, $values)) {
                        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                            $_SESSION['mailing']['receivers'][$receiver->id] = $receiver;
                        }
                    } else {
                        Message::Error('Fallo el SQL!!!!! <br />' . $sql . '<pre>'.print_r($values, true).'</pre>');
                    }

                    // si no hay destinatarios, salta a la lista con mensaje de error
                    if (empty($_SESSION['mailing']['receivers'])) {
                        Message::Error('No se han encontrado destinatarios para ' . $_SESSION['mailing']['filters_txt']);

                        throw new Redirection('/admin/mailing/list');
                    }

                    // si hay, mostramos el formulario de envio
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder'    => 'mailing',
                            'file'      => 'edit',
                            'filters'   => $filters,
                            'interests' => $interests,
                            'status'    => $status,
                            'types'     => $types,
                            'roles'     => $roles,
                            'langs'     => $langs
                        )
                    );

                    break;
                case 'send':

                    $URL = \SITE_URL;

                    $comlang = (isset($_POST['lang'])) ? $_POST['lang'] : \LANG;

                    // Enviando contenido recibido a destinatarios recibidos
                    $receivers = array();

                    $subject = $_POST['subject'];
                    $templateId = !empty($_POST['template']) ? $_POST['template'] : 11;
                    $content = \str_replace('%SITEURL%', $URL, $_POST['content']);

                    // quito usuarios desmarcados
                    foreach ($_SESSION['mailing']['receivers'] as $usr=>$userData) {

                        $errors = array();

                        $campo = 'receiver_'.$usr;
                        if (!isset($_POST[$campo])) {
                            $_SESSION['mailing']['receivers'][$usr]->ok = null;
                        } else {
                            $receivers[] = $userData;
                        }
                    }


                    // montamos el mailing
                    // - se crea un registro de tabla mail
                    $mailHandler = new Mail();
                    $mailHandler->template = $templateId;
                    $mailHandler->content = $content;
                    $mailHandler->node = $node;
                    $mailHandler->lang = $comlang;
                    $mailHandler->massive = true;
                    $mailId = $mailHandler->saveEmailToDB();

                    // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
                    // - initiateSending ($mailId, $subject, $receivers, $autoactive = 0)
                    if (\Goteo\Library\Sender::initiateSending($mailId, $subject, $receivers, 1))  {
                        $ok = true;
                        // Evento Feed
                        $log = new Feed();
                        $log->populate('comunicación masiva a usuarios (admin)', '/admin/mailing',
                            \vsprintf("El admin %s ha iniciado una %s a %s", array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Comunicacion masiva'),
                            $_SESSION['mailing']['filters_txt']
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    } else {
                        $ok = false;
                        // Evento Feed
                        $log = new Feed();
                        $log->populate('comunicación masiva a usuarios (admin)', '/admin/mailing',
                            \vsprintf("El admin %s le ha %s una %s a %s", array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'fallado'),
                            Feed::item('relevant', 'Comunicacion masiva'),
                            $_SESSION['mailing']['filters_txt']
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }


                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder'    => 'mailing',
                            'file'      => 'send',
                            'subject'   => $subject,
                            'interests' => $interests,
                            'status'    => $status,
                            'methods'   => $methods,
                            'types'     => $types,
                            'roles'     => $roles,
                            'langs'     => $langs,
                            'users'     => $receivers,
                            'ok'        => $ok
                        )
                    );

                    break;
            }

            return new View(
                'admin/index.html.php',
                array(
                    'folder'    => 'mailing',
                    'file'      => 'list',
                    'interests' => $interests,
                    'status'    => $status,
                    'methods'   => $methods,
                    'types'     => $types,
                    'roles'     => $roles,
                    'langs'     => $langs,
                    'filters'   => $filters
                )
            );

        }

    }

}
