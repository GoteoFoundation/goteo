<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Text;

    class Bazaar extends \Goteo\Core\Controller {
    
        public function index($id = null, $show = null) {

            $page = Page::get('bazar');

            $URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;
            $page->url = $URL.'/bazaar';
            $lsuf = (LANG != 'es') ? '?lang='.LANG : '';

            $vpath = "view/bazar/";
            $vdata = array();

            if ($id !== null) {
                $item=Model\Bazar::get($id);
                if (!$item instanceof Model\Bazar)
                    throw new Redirection("/bazaar"); 

                $item->imgsrc = (!empty($item->img)) ? '/data/images/'.$item->img->name : '/data/images/bazaritem.svg';

                $vdata["item"] = $item;

                // página interna
                $page->home = false;

                // $project = Model\Project::get($item->project);
                // $vdata["project"] = $project;

                // si el $show es de agradecimiento mostramos thanks.html.php
                if ($show == 'thanks') {
                    $vpath .= "thanks.html.php";
                } else {
                    $vpath .= "page.html.php";
                    // si el $show es de fail datos precargados normalmente desde $_SESSION
                }
             }
             else {
                // portada
                $page->home = true;

                $vdata["items"] = Model\Bazar::getAll();
                $vpath .= "home.html.php";
            }


            // enlaces de compartir
            $bazar_title = $page->name;
            $item_title = !empty($item->title) ? $item->title : $page->name;
            $item_description = !empty($item->description) ? $item->description : $page->description;
            $bazar_url = $page->url.$lsuf;
            $item_url = !empty($item->id) ? $page->url.'/'.$item->id.$lsuf : $page->url.$lsuf;
            $item_image = !empty($item->imgsrc) ? $URL.$item->imgsrc : $URL.'/view/bazar/img/carro.png';

            $vdata["share"] = (object) array(
                'description'=>$page->description, 
                'bazar_url'=>$bazar_url, 
                'bazar_twitter_url'=>'http://twitter.com/home?status=' . urlencode($bazar_title . ': ' . $bazar_url), 
                'bazar_facebook_url'=>'http://facebook.com/sharer.php?u=' . urlencode($bazar_url) . '&t=' . urlencode($bazar_title), 
                'item_url'=>$item_url, 
                'item_twitter_url'=>'http://twitter.com/home?status=' . urlencode($item_title . ': ' . $item_url), 
                'item_facebook_url'=>'http://facebook.com/sharer.php?u=' . urlencode($item_url) . '&t=' . urlencode($item_title), 
            );

            $vdata['page'] = $page;

            $vdata['ogmeta'] = array(
                'title' => $item_title,
                'description' => $item_description,
                'url' => $item_url,
                'image' => $item_image
            );

            return new View($vpath, $vdata);

        }

        /*
        * Metodo para procesar el aporte
        * es como controller/invest::index pero hace varias cosas de modo diferente
        * 
        */
        public function pay($reward = null) {
            if (empty($reward))
                throw new Redirection('/bazaar');

            // sacamos los datos del producto
            $item = Model\Bazar::get($reward);

            // si no es un producto de bazar tenemos un problema

            $message = '';

            // datos del proyecto
            $projectData = Model\Project::get($item->project);

            // metodos habilitados
            $methods = \Goteo\Controller\Invest::$methods;

            // si no está en campaña no pueden esta qui ni de coña, que elijan otro
            if ($projectData->status != 3) {
                throw new Redirection('/bazaar');
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {


                // hago un array de datos
                $formData = array(
                    // el usuario
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],

                    // destinatario de regalo
                    'regalo' => $_POST['regalo'],
                    'namedest' => $_POST['namedest'],
                    'maildest' => $_POST['maildest'],

                    // dirección de destino
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'country'  => $_POST['country'],

                    // mensaje
                    'message'  => $_POST['message']
                );

                die(\trace($formData));

                // metemos los datos en sesión
                $_SESSION['bazar'] = $formData;

                // si el usuario está logueado, usamos los datos de $_SESSION['user']
                // si no creamos un usuario de modo instantaneo

                //@TODO Si el email es de un usuario existente, asignar el aporte a ese usuario
                // si no, creamos un registro de usuario con todo el lio
                $user = \Goteo\Controller\User::instanReg($email);


                $errors = array();
                $los_datos = $_POST;
                $method = \strtolower($_POST['method']);

                // si nos están usando un método no habilitado
                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection("/bazaar/{$reward}/fail");
                }

                // dirección de envio 
                $address = $formData;


                // verificación de impulsor 
                if ($projectData->owner == $user->id) {
                    Message::Error(Text::get('invest-owner-error'));
                    throw new Redirection("/bazaar/{$item->id}/fail");
                }

                // todos los datos de la recompensa
                $rewardData = Model\Project\Reward::get($item->id);

                $invest = new Model\Invest(
                    array(
                        'amount' => $item->amount,
                        'user' => $_SESSION['user']->id,
                        'project' => $item->project,
                        'method' => $method,
                        'status' => '-1',               // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => false,
                        'url' => "/bazar/{$item->id}/" // url de redirección
                    )
                );

                // recompensa
                $invest->rewards = array($item->id);

                $invest->address = (object) $address;

                // también puede ser que genere capital riego...
                // saber si el aporte puede generar riego y cuanto
                if ($projectData->called->dropable) {

                    //@TODO ojo, que no duplique aportes por usuario

                    $invest->called = $projectData->called;
                    $invest->maxdrop = Model\Call\Project::currMaxdrop($projectData, $invest->amount);
                } else {
                    $invest->called = null;
                }

                if ($invest->save($errors)) {
                    $invest->urlOK  = "/invest/confirmed/bazargoteo/{$invest->id}/{$item->id}";
                    $invest->urlNOK = "/invest/fail/bazargoteo/{$invest->id}/{$item->id}";
                    Model\Invest::setDetail($invest->id, 'init', 'Se ha creado el registro de aporte, el usuario ha clickado el boton de tpv o paypal. Proceso controller/invest');

                    switch($method) {
                        case 'tpv':
                            // redireccion al tpv
                            if (Tpv::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-tpv-error_fatal'));
                            }
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            if (Paypal::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-paypal-error_fatal'));
                            }
                            break;
                        case 'cash':
                            $invest->setStatus('1');
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection($invest->urlOK);
                            break;
                    }
                } else {
                    Message::Error(Text::get('invest-create-error'));
                }

            } else {
                Message::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/bazaar/{$reward}/fail");
        }

    }
		
}
